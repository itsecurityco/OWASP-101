from django.conf import settings
from django.contrib import messages
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.db import IntegrityError
from django.http import HttpResponse, HttpResponseRedirect, JsonResponse
from django.shortcuts import render
from django.urls import reverse

from .models import Bank, BeneficiaryBook, Product


@login_required
def index(request):
    return HttpResponseRedirect(reverse("received"))


@login_required
def received(request):
    return render(request, "bank/received.html")


@login_required
def transfer(request):
    return render(request, "bank/transfer.html")

@login_required
def sent(request):
    return render(request, "bank/sent.html")


def sign_in(request):
    if request.method == "POST":
        username = request.POST.get("username")
        password = request.POST.get("password")
        user = authenticate(request, username=username, password=password)

        if user is not None:
            login(request, user)
            return HttpResponseRedirect(reverse('index'))

        messages.add_message(request, messages.WARNING, 'Invalid username and/or password.')
        return render(request, "bank/sign_in.html")

    return render(request, "bank/sign_in.html")


@login_required
def sign_out(request):
    logout(request)
    return HttpResponseRedirect(reverse("sign_in"))


def sign_up(request):
    if request.method == "POST":
        first_name = request.POST.get("first_name")
        username = request.POST.get("username")
        password = request.POST.get("password")

        # create new user
        try:
            user = User.objects.create_user(username=username, password=password)
            user.first_name = first_name
            user.save()
        except IntegrityError:
            messages.add_message(request, messages.WARNING, 'Username already taken.')
            return render(request, "bank/sign_up.html")

        # create user's product
        bank = Bank.objects.get(pk=1)
        Product.objects.create(
            user=user,
            bank=bank
        )
        
        # create default user's beneficiary
        try:
            BeneficiaryBook.objects.create(
                owner=user,
                beneficiary=settings.FLAG_USER_NAME,
                bank=bank,
                product_number=settings.FLAG_USER_ACCOUNT,
            )
        except:
            messages.add_message(
                request, messages.ERROR,
                "Error when creating the user's beneficiary book."
            )
            return render(request, "bank/sign_in.html")

        # return to sing in
        messages.add_message(request, messages.SUCCESS, 'User successfully registered.')
        return HttpResponseRedirect(reverse("sign_in"))
    
    return render(request, "bank/sign_up.html")
