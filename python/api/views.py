import json

from bank.models import Bank, BeneficiaryBook, Product, Transfer
from django.conf import settings
from django.contrib.auth.decorators import login_required
from django.http import JsonResponse


def index(request):
    return JsonResponse({"msg": "Hello. You're at the api index."})

@login_required
def received(request):
    user_account = request.user.products.get().number
    transfers = Transfer.objects.filter(destination=user_account)
    data = []
    for transfer in transfers:
        origin = Product.objects.get(number=transfer.origin.number)
        data.append({
            'id': transfer.id,
            'origin': str(origin.number),
            'origin_name': origin.user.first_name,
            'origin_bank': origin.bank.name,
            'destination': str(transfer.destination),
            'amount': float(transfer.amount),
            'description': transfer.description,
            'date': transfer.created_at.strftime("%d/%m/%Y"),
        })
    return JsonResponse(data, safe=False)

@login_required
def sent(request):
    user_account = request.user.products.get().number
    transfers = Transfer.objects.filter(origin=user_account)
    data = []
    for transfer in transfers:
        try:
            destination = BeneficiaryBook.objects.get(
                product_number=transfer.destination,
                owner=request.user,
            )
            beneficiary_name = destination.beneficiary
            beneficiary_bank = destination.bank.name
        except:
            beneficiary_name = ''
            beneficiary_bank = ''

        data.append({
            'id': transfer.id,
            'origin': str(transfer.origin.number),
            'destination': str(transfer.destination),
            'beneficiary_name': beneficiary_name,
            'beneficiary_bank': beneficiary_bank,
            'amount': float(transfer.amount),
            'description': transfer.description,
            'date': transfer.created_at.strftime("%d/%m/%Y"),
        })
    return JsonResponse(data, safe=False)


@login_required
def accounts(request):
    account = request.user.products.get()
    return JsonResponse({
        "number": account.number,
        "type": dict(Product.TYPE_CHOICES).get(account.type),
        "balance": account.balance,
    })


@login_required
def beneficiaries(request):
    if request.method == "POST":
        data = json.loads(request.body)

        # check account_number (type:int, not:0)
        if type(data["account_number"]) != int or data["account_number"] == 0:
            return JsonResponse({
                "code": "0001",
                "message": {"type": "alert-warning", "content": "Invalid account number."},
            })

        # check beneficiary name
        if data["first_name"] == "":
            return JsonResponse({
                "code": "0001",
                "message": {"type": "alert-warning", "content": "Invalid beneficiary name."},
            })

        # register beneficiary
        try:
            beneficiary_bank = Bank.objects.get(pk=data["bank"])
            BeneficiaryBook.objects.create(
                owner=          request.user,
                beneficiary=    data["first_name"],
                bank=           beneficiary_bank,
                product_type=   data["product_type"],
                product_number= data["account_number"],
            )
        except Bank.DoesNotExist:
            return JsonResponse({
                "code": "0001",
                "message": {"type": "alert-danger", "content": "Invalid bank."},
            })
        except:
            return JsonResponse({
                "code": "0001",
                "message": {"type": "alert-danger", "content": "Failed to register beneficiary. Contact the administrator."},
            })

        # return new beneficiary
        return JsonResponse({
            "code": "0000",
            "message": {"type": "alert-success", "content": "Beneficiary added."},
            "first_name": data["first_name"],
            "bank": beneficiary_bank.name,
            "account_number": data["account_number"],
        })


    beneficiaries = request.user.beneficiaries.all()
    data = []
    for beneficiary in beneficiaries:
        data.append({
            "first_name": beneficiary.beneficiary,
            "bank": beneficiary.bank.name,
            "account_number": beneficiary.product_number,
        })
    return JsonResponse(data, safe=False)


@login_required
def banks(request):
    data = []
    banks = Bank.objects.all()
    for bank in banks:
        data.append({
            "id": bank.id,
            "name": bank.name,
        })
    return JsonResponse(data, safe=False)


@login_required
def product_types(request):
    data = []
    product_types = Product.TYPE_CHOICES
    for product_type in product_types:
        data.append({
            "id": product_type[0],
            "name": product_type[1],
        })
    return JsonResponse(data, safe=False)


@login_required
def transfer(request):
    if request.method == "POST":
        data = json.loads(request.body)

        # check amount (min:1, type:numeric)
        if type(data["amount"]) != int and type(data["amount"]) != float:
            return JsonResponse({
                "code": dict(Transfer.CODES).get(Transfer.FAILED_CODE),
                "message": {"type": "alert-warning", "content": "Invalid amount."},
            })

        if  data["amount"] < 1:
            return JsonResponse({
                "code": dict(Transfer.CODES).get(Transfer.FAILED_CODE),
                "message": {"type": "alert-warning", "content": "The minimum transfer amount is $1."},
            })

        # check that origin account belongs to the current user.
        
        # check origin account exists
        try:
            origin_account = Product.objects.get(pk=data["origin"])
        except Product.DoesNotExist:
            return JsonResponse({
                "code": dict(Transfer.CODES).get(Transfer.FAILED_CODE),
                "message": {"type": "alert-danger", "content": "The origin account does not exist."},
            })
        
        # check origin account has funds to make the transfer
        if origin_account.balance < data["amount"]:
            return JsonResponse({
                "code": dict(Transfer.CODES).get(Transfer.FAILED_CODE),
                "message": {
                    "type": "alert-warning",
                    "content": "The source account does not have enough funds to make the transfer."
                },
            })
        
        # check destination bank exists
        try:
            destination_bank = Bank.objects.get(name=data["beneficiary_bank"])
        except Bank.DoesNotExist:
            return JsonResponse({
                "code": dict(Transfer.CODES).get(Transfer.FAILED_CODE),
                "message": {"type": "alert-warning", "content": "The beneficiary bank does not exist."},
            })

        # check destination account exists
        try:
            destination_account = Product.objects.get(
                pk=data["beneficiary_account"],
                bank=destination_bank,
            )
            external_bank = False
        except Product.DoesNotExist:
            if destination_bank.id == 1:
                return JsonResponse({
                    "code": dict(Transfer.CODES).get(Transfer.FAILED_CODE),
                    "message": {"type": "alert-danger", "content": "The beneficiary account does not exist."},
                })
            # transfer goes to an external bank
            external_bank = True

        # withdraw amount from origin account
        origin_account.balance -= data["amount"]
        origin_account.save()

        # inject amount to beneficiary account
        if not external_bank:
            destination_account.balance += data["amount"]
            destination_account.save()

        # register transfer
        try:
            Transfer.objects.create(
                origin=         origin_account,
                destination=    data["beneficiary_account"],
                amount=         data["amount"],
                description=    data["description"],
            )
        except:
            return JsonResponse({
                "code": dict(Transfer.CODES).get(Transfer.FAILED_CODE),
                "message": {"type": "alert-danger", "content": "Failed to register transfer. Contact the administrator."},
            })

        # return flag
        if origin_account.number == settings.FLAG_USER_ACCOUNT:
            return JsonResponse({
                "code": dict(Transfer.CODES).get(Transfer.APPROVED_CODE),
                "message": {"type": "alert-success", "content": "Approved transaction."},
                "originAccountBalance": origin_account.balance,
                "flag": settings.FLAG,
            })

        # return origin account balance
        return JsonResponse({
            "code": dict(Transfer.CODES).get(Transfer.APPROVED_CODE),
            "message": {"type": "alert-success", "content": "Approved transaction."},
            "originAccountBalance": origin_account.balance,
        })


@login_required
def transfer_detail(request, transfer_id):
    try:
        transaction = Transfer.objects.get(pk=transfer_id)
        return JsonResponse({
            'id': transaction.id,
            'origin': {
                'fullname': transaction.origin.user.first_name,
                'product': str(transaction.origin.number),
            },
            'destination': {
                'product': str(transaction.destination),
            },
            'amount': float(transaction.amount),
            'description': transaction.description,
            'date': transaction.created_at.strftime("%d/%m/%Y %H:%M:%S"),
        }, safe=False)
    except Transfer.DoesNotExist:
        return JsonResponse([], safe=False)
