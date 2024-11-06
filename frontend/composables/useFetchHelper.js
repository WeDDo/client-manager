export function useFetchHelper() {
    const toast = useToast();
    const router = useRouter();

    const token = useCookie('token');

    const mainStore = useMainStore();

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

    async function handleResponseError(response, form) {
        switch (response.status) {
        case 400:
            toast.add({severity: 'error', summary: response._data.error, life: 5000});
            break;
        case 401:
        case 405:
            if(router.currentRoute.value.fullPath === '/login') {
                router.push('/login');
                token.value = null;
            }

            toast.add({severity: 'error', summary: 'Authorisation error!', life: 5000});
            break;
        case 422:
            let message = 'Please correctly fill out the fields';

            if (form) {
                if (response._data.errors) {
                    Object.keys(response._data.errors).forEach((field) => {
                        const errorMessages = response._data.errors[field];
                        form.setFieldError(`item.${field}`, errorMessages[0]);
                    });
                }
            }

            if (response._data.message) {
                message = response._data.message;
            }

            toast.add({severity: 'error', summary: message, life: 5000});
            break;
        default:
            toast.add({severity: 'error', summary: 'Server error!', life: 5000});
        }
    }

    function handleResponseAutocompleteData(data, form) {
        if (!data.additional?.autocomplete_data) return;

        const autocompleteData = data.additional?.autocomplete_data;

        const mergedData = { ...data };

        mergedData.item = { ...mergedData.item, ...Object.fromEntries(
                Object.entries(autocompleteData).map(([key, value]) => [key, value.item || null])
            )};

        form.setValues(mergedData);
    }

    function handleRouteTo(routeName, response = null, formType = 'create') {
        if(mainStore.settings.on_create_go_to_edit_form && formType === 'create') {
            if(response._data.item.id) {
                router.push(`${routeName}/${response._data.item.id}`);
                return;
            }
        }

        if(formType === 'update') {

        }

        router.push(routeName);
    }

    function getDataTableUrl(url, event) {
        const filterParams = (event?.filters || [])
            .map(filter => {
                if (!filter.value) return null;

                const field = encodeURIComponent(filter.name);
                const operator = encodeURIComponent(filter.operator || '=');
                const value = encodeURIComponent(filter.value);

                return `filters[${field}][name]=${field}&filters[${field}][operator]=${operator}&filters[${field}][value]=${value}`;
            })
            .filter(Boolean)
            .join('&');

        return `${url}?page=${event.page + 1}&sort_field=${event.sort_field || ''}&sort_order=${event.sort_order || ''}&${filterParams}`;
    }

    return {
        handleDownloadBlob,
        handleResponseError,
        handleUseFetchError,

        handleResponseAutocompleteData,

        handleRouteTo,
        getDataTableUrl,
    };
}
