export function useInsideFormValidation(errors, emit, tab) {
    watch(errors, async (newValues) => {
        emit('set-errors', {errors: newValues, tab: tab});
    });

    return {};
}
