import useAxios from "./service";

const {postData} = useAxios()

function todayDate(){
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

export {registerDeviceToken, todayDate}
