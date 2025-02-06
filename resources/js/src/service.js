import { ref } from 'vue';
import axios from 'axios'

export default function useAxios() {
    const result = ref({
        success: true,
        message: 'success',
        result: null,
    });

    const base_url = '/api/panel/'
    function setHeader() {
        const token = localStorage.getItem('user_token');
        return {
            'Authorization': 'Bearer ' + token
        }
    }

    function handleError(e) {
        if (e.response.status === 401) {
            window.location = '/auth/login'
            result.value.success = false;
        } else if (e.response.status === 422) {
            result.value.success = false;
            result.value.errors = e.response.data.errors;
        } else {
            alert(e.response.data.message)
            result.value.success = false;
        }
    }

    function handleFalse(data) {
        if (data.result) {
            alert(data.result)
        }
    }

    async function getData(url, params) {
        await axios.get(base_url + url, { params: params, headers: setHeader() })
            .then(({ data }) => {
                result.value = data
            }).catch((e) => {
                handleError(e);
            });

        if (result.value.success) {
            return result.value
        } else {
            return result.value;
        }
    }

    async function postData(url, data) {
        await axios.post(base_url + url, data, {
            headers: setHeader()
        }).then(({ data }) => {
            result.value = data
        }).catch((e) => {
            handleError(e);
        });

        if (result.value.success) {
            return result.value
        } else {
            return result.value;
        }
    }

    async function basePostData(url, data) {
        return await axios.post(base_url + url, data, {
            headers: setHeader()
        });
    }

    async function patchData(url, data) {
        try {
            const response = await axios.patch(base_url + url, data, {
                headers: setHeader()
            });
            result.value = response.data;
        } catch (e) {
            handleError(e);
        }

        return result.value;
    }

    async function deleteData(url, data) {
        await axios.delete(base_url + url, {
            headers: setHeader(),
            data: data
        }).then(({ data }) => {
            result.value = data
        }).catch((e) => {
            handleError(e);
        });

        if (result.value.success) {
            return result.value
        } else {
            return result.value;
        }
    }

    return {
        result,
        getData,
        postData,
        patchData,
        deleteData,
        basePostData
    };
}
