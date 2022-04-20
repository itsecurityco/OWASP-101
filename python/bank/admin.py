from django.contrib import admin

from .models import Bank, Product, Transfer, BeneficiaryBook


class BankAdmin(admin.ModelAdmin):
    list_display = ('id', 'name')

class ProductAdmin(admin.ModelAdmin):
    list_display = ('number', 'user', 'bank', 'type')

class TransferAdmin(admin.ModelAdmin):
    list_display = ('id', 'origin', 'destination', 'amount')

class BeneficiaryBookAdmin(admin.ModelAdmin):
    list_display = ('id', 'owner', 'beneficiary')


admin.site.register(Bank, BankAdmin)
admin.site.register(Product, ProductAdmin)
admin.site.register(Transfer, TransferAdmin)
admin.site.register(BeneficiaryBook, BeneficiaryBookAdmin)