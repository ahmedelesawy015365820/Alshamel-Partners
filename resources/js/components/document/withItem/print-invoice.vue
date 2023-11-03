<template>
    <div class="invoice-box" id="printReservation">
        <table>
            <tr class="top">
                <td colspan="6">
                    <table>
                        <tr>
                            <td class="title">
<!--                                <img src="./images/addeffect-logo.webp" alt="Company logo" style="width: 100%; max-width: 300px" />-->
                            </td>

                            <td>
                                Invoice NO: {{ document_data.prefix }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td>
                                Agency: Times International for Advertising & Publicity<br />
                                Client: {{ document_data.customer.name_e }}
                            </td>

                            <td>
                                Invoice Issue Date: {{ document_data.date }}<br />
                                Client Ref:..........................
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr >
                <td colspan="2">Media</td>

                <td colspan="1">from Date</td>

                <td colspan="1">to Date</td>

                <td colspan="2">Notes</td>
            </tr>

            <tr class="heading">
                <td colspan="6">MEGA</td>
            </tr>

            <tr class="item">
                <td colspan="2">Mega Hawally 40 Faces -2 Weeks</td>

                <td colspan="1">14/05/2023</td>

                <td colspan="1">27/05/2023</td>

                <td colspan="2"></td>
            </tr>
            <tr class="item">
                <td colspan="2">Mega Hawally 40 Faces -2 Weeks</td>

                <td colspan="1">14/05/2023</td>

                <td colspan="1">27/05/2023</td>

                <td colspan="2"></td>
            </tr>
            <tr class="item">
                <td colspan="2">Mega Hawally 40 Faces -2 Weeks</td>

                <td colspan="1">14/05/2023</td>

                <td colspan="1">27/05/2023</td>

                <td colspan="2"></td>
            </tr>
            <tr class="item">
                <td colspan="2">Mega Hawally 40 Faces -2 Weeks</td>

                <td colspan="1">14/05/2023</td>

                <td colspan="1">27/05/2023</td>

                <td colspan="2"></td>
            </tr>
            <tr class="item">
                <td colspan="2">Mega Hawally 40 Faces -2 Weeks</td>

                <td colspan="1">14/05/2023</td>

                <td colspan="1">27/05/2023</td>

                <td colspan="2"></td>
            </tr>

            <tr class="total">
                <td colspan="4">ONLY ONE HUNDRED FIFTEEN THOUSAND SEVEN HUNDRED SEVENTY-SIX AND 500 / 1000 KD</td>

                <td colspan="2">Net Total KD: 115,776.50</td>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    name: "print-invoice",
    props: {
        document_data: {
            default: '',
        },
    },
}
</script>

<style scoped>
.invoice-box {
    max-width: 800px;
    margin: auto;
    padding: 30px;
    border: 1px solid #eee;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
    font-size: 16px;
    line-height: 24px;
    font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    color: #555;
}

.invoice-box table {
    width: 100%;
    line-height: inherit;
    text-align: left;
    border-collapse: collapse;
}

.invoice-box table td {
    padding: 5px;
    vertical-align: top;
}

.invoice-box table tr td:nth-child(2) {
    text-align: right;
}

.invoice-box table tr.top table td {
    padding-bottom: 20px;
}

.invoice-box table tr.top table td.title {
    font-size: 45px;
    line-height: 45px;
    color: #333;
}

.invoice-box table tr.information table td {
    padding-bottom: 40px;
}

.invoice-box table tr.heading td {
    background: #eee;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
}

.invoice-box table tr.details td {
    padding-bottom: 20px;
}

.invoice-box table tr.item td {
    border-bottom: 1px solid #eee;
}

.invoice-box table tr.item.last td {
    border-bottom: none;
}

.invoice-box table tr.total td:nth-child(2) {
    border-top: 2px solid #eee;
    font-weight: bold;
}

@media only screen and (max-width: 600px) {
    .invoice-box table tr.top table td {
        width: 100%;
        display: block;
        text-align: center;
    }

    .invoice-box table tr.information table td {
        width: 100%;
        display: block;
        text-align: center;
    }
}
</style>
