from decimal import Decimal

from django.conf import settings
from django.contrib.auth.models import User
from django.db import models


# Create your models here.
class Bank(models.Model):
    name = models.CharField(max_length=100, null=False)

    def __str__(self) -> str:
        return self.name


class Product(models.Model):
    number = models.BigAutoField(primary_key=True)
    user = models.ForeignKey(
        User,
        on_delete=models.CASCADE,
        related_name="products",
    )
    balance = models.DecimalField(
        max_digits=19,
        decimal_places=2,
        default=Decimal(settings.INITIAL_DEPOSIT),
        null=True,
    )
    bank = models.ForeignKey(
        Bank,
        on_delete=models.CASCADE,
        related_name="bank_products",
    )
    CHECKING_ACCOUNT, SAVING_ACCOUNT = 'CA', 'SA'
    TYPE_CHOICES = [
        (CHECKING_ACCOUNT, 'Checking Account'),
        (SAVING_ACCOUNT, 'Saving Account'),
    ]
    type = models.CharField(
        max_length=2,
        choices=TYPE_CHOICES,
        default=CHECKING_ACCOUNT,
    )

    def serialize(self):
        return {
            "number": self.number,
            "balance": self.balance,
            "bank": self.bank.id,
            "type": self.type,
        }


class Transfer(models.Model):
    # Transfer codes
    APPROVED_CODE = "APPROVED"
    FAILED_CODE   = "FAILED"
    CODES = [
        (APPROVED_CODE, '0000'),
        (FAILED_CODE, '0001'),
    ]

    origin = models.ForeignKey(
        Product,
        on_delete=models.CASCADE,
        related_name="transfers",
    )
    destination = models.BigIntegerField(null=False, blank=False)
    amount = models.DecimalField(
        max_digits=19,
        decimal_places=2,
        null=False,
        blank=False,
    )
    description = models.CharField(max_length=255, null=True)
    created_at = models.DateTimeField(auto_now_add=True)


class BeneficiaryBook(models.Model):
    owner = models.ForeignKey(
        User,
        on_delete=models.CASCADE,
        related_name="beneficiaries",
    )
    beneficiary = models.CharField(max_length=255)
    bank = models.ForeignKey(
        Bank,
        on_delete=models.CASCADE,
        related_name="+",
    )
    product_type = models.CharField(
        max_length=2,
        choices=Product.TYPE_CHOICES,
        default=Product.CHECKING_ACCOUNT,
    )
    product_number = models.BigIntegerField(null=False, blank=False)