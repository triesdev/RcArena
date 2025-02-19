import useAxios from "./service";

const { postData } = useAxios()

function todayDate() {
    return '2024-01-29';
}

function registerDeviceToken(client_token, station_id = null) {
    let device_token = localStorage.getItem('ant_device_token')
    if (device_token === null) {
        device_token = makeToken(60)
    }

    postData('register-device-token', {
        device_token: device_token,
        client_token: client_token,
        station_id: station_id,
    }).then((data) => {
        if (data.success) {
            localStorage.setItem('ant_device_token', device_token)
        } else {
            registerDeviceToken()
            localStorage.removeItem('ant_device_token')
        }
    })
}

function makeToken(length) {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    let counter = 0;
    while (counter < length) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
        counter += 1;
    }
    return result;
}

function formatDateAndTime(date) {
    const d = new Date(date)
    const year = d.getFullYear()
    const month = '02';
    const day = d.getDate()
    const hour = d.getHours()
    const minute = d.getMinutes()
    return `${year}-${month}-${day} ${hour}:${minute}`
}

export { registerDeviceToken, todayDate, makeToken, formatDateAndTime }
