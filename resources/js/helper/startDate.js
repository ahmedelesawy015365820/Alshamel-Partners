export function formatDateTime(item) {
    let now = new Date(item);
    let st = now.toISOString().match(/(\d{4}\-\d{2}\-\d{2})T(\d{2}:\d{2}:\d{2})/)
    return st[1] + ' ' + st[2];
};

export function formatDateOnly(item) {
    let now = new Date(item);
    let st = now.toISOString().match(/(\d{4}\-\d{2}\-\d{2})T(\d{2}:\d{2}:\d{2})/)
    return st[1];
};
export function formatTime(item) {
    var d = new Date(item); // for now
    return `${d.getHours()}:${d.getMinutes()}:${d.getSeconds()}`;
}
export function tConvert(time) {
    // Check correct time format and split into components
    time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

    if (time.length > 1) { // If time format correct
        time = time.slice(1);  // Remove full string match value
        time[5] = +time[0] < 12 ? 'AM' : 'PM'; // Set AM/PM
        time[0] = +time[0] % 12 || 12; // Adjust hours
    }
    return time.join(''); // return adjusted time or original string
}
