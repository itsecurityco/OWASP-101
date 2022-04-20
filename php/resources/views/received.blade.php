@extends('main')

@section('content')
<main role="main" class="container">
    <div class="starter-template">
        <div class="mb-5 text-center">
            <h2>Transfers</h2>
            <p class="lead">Received</p>
        </div>
        <div class="table-responsive">
            <table class="table table-secondary table-striped table-md">
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-start">Destination Account</th>
                        <th class="text-start">Origin Account</th>
                        <th class="text-start">Origin Name</th>
                        <th class="text-start">Origin Bank</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="transfer_body"></tbody>
            </table>
            <div id="message"></div>
            <div class="text-end">
                <a href="/transfer" class="btn btn-dark float-right">Make a Transfer</a>
            </div>
        </div>
    </div>
</main>
@endsection

@section('javascript')
<script type="text/javascript">
    fetch('/api/received', {
        method: "GET",
    })
    .then(response => response.json())
    .then(result => {
        let html = ''
        if (! result.length){
            html += '<div class="alert alert-secondary" role="alert">'
            html += 'Your account has not received transfers yet'
            html += '</div>'
            document.getElementById("message").innerHTML = html
        } else {
            result.forEach(transfer => {
                html += `</td><td class='text-center'>${transfer.date}</td>`
                html += `<td class='text-start'>${transfer.destination}</td>`
                html += `<td class='text-start'>${transfer.origin}</td>`
                html += `<td class='text-start'>${transfer.origin_name}</td>`
                html += `<td class='text-start'>${transfer.origin_bank}</td>`
                html += `<td class='text-end'>$${transfer.amount}</td>`
                html += `<td class='text-center'><span class='badge bg-secondary'>ACCEPTED</span></td>`
                html += `<td class='text-start'><span onclick='showDetail(${transfer.id})'>`
                    html += `<i class='bi bi-plus-square'></i></span></td></tr>`
                html += `<tr id='tdetail-${transfer.id}' class='tdetail d-none'><td colspan='4' class='text-start text-muted ps-3 bg-light'>`
                    html += `<p><strong>Detail of transfer received</strong></p>`
                    html += `Comment: ${transfer.message}</td>`
                html += `<td colspan='4' class='text-start text-muted align-middle bg-light'>ID Transfer ${transfer.id}</td></tr>`
            })
            document.getElementById('transfer_body').innerHTML = html;
        }
    })

    function showDetail(id) {
        detailClicked = document.querySelector(`#tdetail-${id}`)
        if (detailClicked.classList.contains('d-none')) {
            detailElements = document.querySelectorAll('.tdetail')
            detailElements.forEach(detailElement => {
                detailElement.classList.add("d-none")
            })
            detailClicked.classList.remove('d-none')
        } else {
            detailClicked.classList.add('d-none')
        }
    }
</script>
@endsection