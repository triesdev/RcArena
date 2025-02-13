import { reactive } from "vue";
const state = reactive({
    errors: []
})

export default function useValidation() {

    function getStatus(field) {
        const errors = state.errors
        return errors[field] !== undefined
    }

    function getMessage(field) {
        const errors = state.errors
        if (getStatus(field)) {
            return errors[field][0]
        }
    }

    function setErrors(errors) {
        state.errors = errors
    }

    // Remove a specific error field from the errors object
    const removeError = (field) => {
        if (state.errors[field]) {
            delete state.errors[field];
        }
    };

    function resetErrors() {
        state.errors = []
    }

    return {
        getStatus,
        getMessage,
        setErrors,
        resetErrors,
        removeError
    }
}
