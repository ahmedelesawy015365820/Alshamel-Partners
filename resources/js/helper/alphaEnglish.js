export default function (value) {
    let re = /^[a-zA-Z ]+$/;
    return re.test(value);
}
