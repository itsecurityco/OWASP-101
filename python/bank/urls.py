from django.urls import path

from . import views

urlpatterns = [
    path('', views.index, name="index"),
    path('sign-in', views.sign_in, name="sign_in"),
    path("sign-out", views.sign_out, name="sign_out"),
    path('sign-up', views.sign_up, name="sign_up"),
    path('home', views.index, name="home"),
    path('received', views.received, name="received"),
    path('sent', views.sent, name="sent"),
    path('transfer', views.transfer, name="transfer"),
]