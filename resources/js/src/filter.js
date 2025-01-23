import moment from 'moment';

const config = {
    currency(value) {
        if (value && !isNaN(value)) {
            return value.toLocaleString('id-ID')
        }

        return '-'
    },
    capitalize(value) {
        const words = value.split(" ");

        for (let i = 0; i < words.length; i++) {
            if (words[i][0]) {
                words[i] = words[i][0].toUpperCase() + words[i].substr(1).toLowerCase();
            }
        }

        return words.join(" ");
    },
    truncate(text, length = 20, clamp = '...') {
        var node = document.createElement('div');
        node.innerHTML = text;
        var content = node.textContent;
        return content.length > length ? content.slice(0, length) + clamp : content;
    },
    initial(text, length = 2) {
        let words = text.split(" ")
        let initial = "";
        for (let i = 0; i < length; i++) {
            if (words[i]) {
                initial = initial + words[i][0]
            }
        }

        return initial;
    },
    ucWord(value) {
        if (!value) return '';
        value = value.toString();
        value = value.toLowerCase();
        return (value + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    },
    formatDateMonth(value) {
        if (value) {
            return moment(value).format("DD MMM");
        }
    },
    formatDayName(value) {
        if (value) {
            return moment(value).format("dddd");
        }
    },
    formatDate(value) {
        if (value) {
            return moment(value).format("DD MMM YYYY");
        }
    },
    formatDateTime(value) {
        if (value) {
            return moment(value).format("DD MMM YYYY HH:mm");
        }
    },
    formatTime(value) {
        if (value) {
            return moment(value).format("HH:mm");
        }
    },
    formatTimes(value) {
        if (value) {
            return moment(value).format("HH:mm:ss");
        }
    },
}

export default config;
