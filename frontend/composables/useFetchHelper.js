export function useFetchHelper() {
    const toast = useToast();
    const router = useRouter();

    const token = useCookie('token');

    async function handleDownloadBlob(response, filename = 'filename.zip') {
        const url = window.URL.createObjectURL(new Blob([response._data]));
        const contentDisposition = response.headers.get('Content-Disposition')

        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename\*?="?([^"]+)"?;?/);
            if (filenameMatch && filenameMatch.length > 1) {
                filename = decodeURIComponent(filenameMatch[1]);
            }
        }

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        toast.add({severity: 'success', summary: 'Download successful!', life: 2000});
    }

    function handleUseFetchError(error) {
        switch (error.value.statusCode) {
        case 401:
        case 405:
            if(router.currentRoute.value.fullPath !== '/login') {
                router.push('/login');
                token.value = null;
            }

            toast.add({severity: 'error', summary: 'Authorisation error!', life: 5000});
            break;
        default:
            if (!token.value) {
                if(router.currentRoute.value.fullPath !== '/login') {
                    router.push('/login');
                }
            }
            toast.add({severity: 'error', summary: 'Server error!', life: 5000});
        }
    }

    async function handleResponseError(response) {
        switch (response.status) {
        case 401:
        case 405:
            if(router.currentRoute.value.fullPath === '/login') {
                router.push('/login');
                token.value = null;
            }

            toast.add({severity: 'error', summary: 'Authorisation error!', life: 5000});
            break;
        default:
            toast.add({severity: 'error', summary: 'Server error!', life: 5000});
        }

    }

    return {
        handleDownloadBlob,
        handleResponseError,
        handleUseFetchError,
    };
}
