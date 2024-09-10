export function useFormHelper(formValues, tabs = null) {
    const toast = useToast();
    const errors = reactive({});

    async function validateForm(errors) {
        let errorInTabs = false;

        for (const tab of tabs) {
            if (typeof tab.ref?.onSubmit === 'function') {
                const submitResult = await tab.ref.onSubmit();
                if (!submitResult) {
                    errorInTabs = true;
                    errors[tab.name] = true;
                } else {
                    errors[tab.name] = false;
                }
            }
        }

        if(errorInTabs) {
            toast.add({ severity: 'error', summary: 'Please correctly fill out the fields', life: 5000 });
        }

        return !errorInTabs;
    }

    function setFormValues(values) {
        Object.assign(formValues, values);
    }

    async function setErrors(event) {
        tabs[event.tab].errors = event.errors;
    }

    return {
        errors,
        setErrors,
        setFormValues,
        validateForm
    };
}
