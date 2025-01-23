import {reactive} from "vue";
const state = reactive({
    errors: []
})

export default function useValidation(){

    function getStatus(field){
        const errors = state.errors
        return errors[field] !== undefined
    }

    function getMessage(field){
        const errors = state.errors
        if(getStatus(field)){
            return errors[field][0]
        }
    }

    function setErrors(errors){
        state.errors = errors
    }

    function resetErrors(){
        state.errors = []
    }

    return {
        getStatus,
        getMessage,
        setErrors,
        resetErrors
    }
}
