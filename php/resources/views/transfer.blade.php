@extends('main')

@section('content')
<main role="main" class="container">
    <div class="starter-template">
        <div class="mb-5 text-center">
            <h2>Transfer</h2>
            <p class="lead">Make a Transfer</p>
        </div>
        <div id="form">
            @csrf
            <div class="row">
                <div class="col-md-8 order-md-1">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="origin">Origin Account</label>
                            <input type="text" class="form-control" id="text_origin" disabled>
                            <input type="hidden" id="origin" name="origin" value="">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dest">Beneficiary</label>
                            <select class="form-select d-block w-100" id="book_list" required="" onchange="update_details(this)">
                            </select>
                            <input type="hidden" id="destination" name="destination" value="">
                            <input type="hidden" id="bank" name="bank" value="">
                            <input type="hidden" id="name" name="name" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="amount">Amount to Transfer</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                                </div>
                                <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter amount" required="">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 text-end">
                            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modal_book">
                                <span class="badge rounded-pill bg-light"><i class='bi bi-plus-circle text-dark'></i></span> <span class="text-light">New Beneficiary</span>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label" for="message" class="float-left">Message</label>
                            <input type="text" class="form-control" id="message" name="message" placeholder="Optional message">
                        </div>
                    </div>
                    <div class="row mt-3 mb-3">
                        <div class="col-md-12 text-end">
                            <input class="btn btn-dark" type="button" value="Transfer" onclick="makeTransfer()">
                            <a class="btn btn-secondary" href="/transfer">Cancel</a>
                        </div>
                    </div>
                    <div class="row">
                        <div id="alert" class="col-md-12"></div>
                    </div>
                </div>
                <div class="col-md-4 order-md-2 text-dark mt-2">
                    <ul class="list-group mb-3 mt-4">
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div><h6 class="my-0">Account Balance</h6></div>
                            <div class="text-success"><h6 class="my-0" id="details_balance"></h6></div>
                        </li>
                    </ul>
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Transfer Detail</span>
                    </h4>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div><h6 class="my-0">Beneficiary</h6></div>
                            <span class="font-weight-light" id="details_name"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div><h6 class="my-0">Beneficiary Account</h6></div>
                            <span class="font-weight-light" id="details_product"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div><h6 class="my-0">Beneficiary Bank</h6></div>
                            <span class="text-uppercase" id="details_bank"></span>
                        </li>
                    </ul>
                <div>
            </div>
        </div>
        <!-- modal add new destinatary to book -->
        <div class="modal fade" tabindex="-1" id="modal_book">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Beneficiary</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/agenda" method="post">
                        <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="modal_name">Name*</label>
                                    <input type="text" id="modal_name" name="fullname" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="modal_bank">Bank*</label>
                                    <select class="form-select" id="modal_bank" name="bank" required></select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label" for="modal_type">Account type*</label>
                                    <select class="form-select" id="modal_type" name="product_type" required></select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label" for="modal_account">Account number*</label>
                                    <input type="text" id="modal_product" name="product" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-dark" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection

@section('javascript')
    <script type="text/javascript">
        // get user products
        fetch('/api/accounts', {
            method: "GET",
        })
        .then(response => response.json())
        .then(result => {
            document.getElementById('origin').value = result.id
            document.getElementById('text_origin').placeholder = `${ result.type } ${ result.id }`
            document.getElementById('details_balance').innerText = new Intl.NumberFormat('en-US', { 
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
            }).format(result.balance)
        })
        // get user book list
        fetch('/api/beneficiaries', {
            method: "GET",
        })
        .then(response => response.json())
        .then(result => {
            result.forEach(beneficiary => {
                let option   = document.createElement('option');
                option.value = beneficiary.product_number;
                option.text  = `${ beneficiary.fullname } - ${ beneficiary.bank }`;
                let select   = document.getElementById('book_list');
                select.appendChild(option);
            })
            // setup transfer details
            document.getElementById('name').value    = result[0].fullname;
            document.getElementById('bank').value    = result[0].bank;
            document.getElementById('destination').value = result[0].product_number;
            document.getElementById('details_name').innerText    = result[0].fullname;
            document.getElementById('details_product').innerText = result[0].product_number;
            document.getElementById('details_bank').innerText    = result[0].bank;
        })
        // update transfer details
        function update_details(book_list) {
            let account = book_list.options[book_list.selectedIndex].value;
            let beneficiary_data = book_list.options[book_list.selectedIndex].text.split('-');
            document.getElementById('name').value    = beneficiary_data[0].trim();
            document.getElementById('bank').value    = beneficiary_data[1].trim();
            document.getElementById('destination').value = account;
            document.getElementById('details_name').innerText    = beneficiary_data[0].trim();
            document.getElementById('details_product').innerText = account;
            document.getElementById('details_bank').innerText    = beneficiary_data[1].trim();
        }
        // fill banks modal list
        fetch('/api/banks', {
            method: "GET",
        })
        .then(response => response.json())
        .then(result => {
            result.forEach(bank => {
                let option = document.createElement('option');
                option.value = bank.id;
                option.text  = bank.name;
                let select = document.getElementById('modal_bank');
                select.appendChild(option);
            })
        })
        // fill product types modal list
        fetch('/api/product_types', {
            method: "GET",
        })
        .then(response => response.json())
        .then(result => {
            result.forEach(type => {
                let option   = document.createElement('option');
                option.value = type.id;
                option.text  = type.name;
                let select = document.getElementById('modal_type');
                select.appendChild(option);
            })
        })
        // make transfer
        function makeTransfer() {
            const csrftoken = document.querySelector('[name=_token]').value;
            let data = new URLSearchParams();
            data.append('origin', Number(document.querySelector('[name=origin]').value));
            data.append('destination', Number(document.querySelector('[name=destination]').value));
            data.append('amount', Number(document.querySelector('[name=amount]').value));
            data.append('bank', document.querySelector('[name=bank]').value);
            data.append('name', document.querySelector('[name=name]').value);
            data.append('message', document.querySelector('[name=message]').value);
            data.append('_token', csrftoken);

            fetch('/api/transfer', {
                method: 'POST',
                body: data,
            })
            .then(response => response.json())
            .then(result => {
                if (result.code == "0000") {
                    document.querySelector("#details_balance").innerText = new Intl.NumberFormat('en-US', { 
                        style: 'currency',
                        currency: 'USD',
                        minimumFractionDigits: 0,
                    }).format(result.originAccountBalance)
                }
                let html = `<div class="alert ${result.message.type} alert-dismissible fade show" role="alert">`
                html += result.message.content
                html += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                html += '</div>'
                document.querySelector("#alert").innerHTML = html
                document.querySelector('[name=amount]').value = ''
                document.querySelector('[name=message]').value = ''

            })
        }
    </script>
@endsection