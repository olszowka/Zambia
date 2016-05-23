"""zd URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/1.8/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  url(r'^$', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  url(r'^$', Home.as_view(), name='home')
Including another URLconf
    1. Add a URL to urlpatterns:  url(r'^blog/', include('blog.urls'))
"""
from django.conf.urls import include, url
from django.contrib import admin
from zambia import views

urlpatterns = [
    url(r'^$', views.index, name='index'),
    url(r'^login$', views.view_login, {'reason': None}, name='login'),
    url(r'^logout$', views.view_logout, name='logout'),
    url(r'^StaffPage$', views.staff_page, name='StaffPage'),
    url(r'^welcome$', views.welcome, name='welcome'),
    url(r'^BrainstormWelcome$',         views.brainstorm_welcome,        name='BrainstormWelcome'),
    url(r'^BrainstormSearchSession$',   views.brainstorm_search_session, name='BrainstormSearchSession'),
    url(r'^BrainstormCreateSession$',   views.brainstorm_create_session, name='BrainstormCreateSession'),
    url(r'^BrainstormReportAll$',       views.brainstorm_report,         {'qtype': 'All'},       name='BrainstormReportAll'),
    url(r'^BrainstormReportUnseen$',    views.brainstorm_report,         {'qtype': 'Unseen'},    name='BrainstormReportUnseen'),
    url(r'^BrainstormReportReviewed$',  views.brainstorm_report,         {'qtype': 'Reviewed'},  name='BrainstormReportReviewed'),
    url(r'^BrainstormReportLikely$',    views.brainstorm_report,         {'qtype': 'Likely'},    name='BrainstormReportLikely'),
    url(r'^BrainstormReportScheduled$', views.brainstorm_report,         {'qtype': 'Scheduled'}, name='BrainstormReportScheduled'),
    url(r'^admin/', include(admin.site.urls)),
]
