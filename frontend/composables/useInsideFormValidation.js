export function useInsideFormValidation(values, errors, emit, tab) {
    watch(values, async (newValues) => {
        emit('set-form-values', newValues);
    });

    watch(errors, async (newValues) => {
        emit('set-errors', {errors: newValues, tab: tab});
    });

    return {};
}
