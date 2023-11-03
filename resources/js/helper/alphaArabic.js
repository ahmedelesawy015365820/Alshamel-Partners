export default function (value) {
    let re = /^[\u0621-\u064A ]+$/;
    return re.test(value);
}
