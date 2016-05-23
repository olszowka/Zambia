from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.http import HttpResponse
from django.template import loader
from django.shortcuts import redirect,render


def view_login(request, reason = None):
    if request.method == 'POST':
        username = request.POST['username']
        password = request.POST['password']
 	user = authenticate(username=username, password=password)
	if user is not None:
            if user.is_active:
		login(request, user)
		return redirect('index')
            else:
                reason='Not active'
	else:
            reason='Invalid user or password'
    template = loader.get_template('zambia/login.html')
    context = {
	'reason': reason,
    }
    return HttpResponse(template.render(context, request))

def view_logout(request):
    logout(request)
    return redirect('login')

@login_required
def index(request):
    template = loader.get_template('zambia/index.html')
    context = {}
    return HttpResponse(template.render(context, request))

