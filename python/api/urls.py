from django.urls import path

from . import views

urlpatterns = [
    path('', views.index, name='api_index'),
    path('received', views.received, name='api_received'),
    path('sent', views.sent, name='api_sent'),
    path('accounts', views.accounts, name='api_accounts'),
    path('beneficiaries', views.beneficiaries, name='api_beneficiaries'),
    path('banks', views.banks, name='api_banks'),
    path('product_types', views.product_types, name='api_product_types'),
    path('transfer', views.transfer, name='api_transfer'),
    path('transfer/<int:transfer_id>', views.transfer_detail, name='api_transfer_detail'),
]