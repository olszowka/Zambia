from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.http import HttpResponse
from django.template import loader
from django.shortcuts import redirect,render
from django.utils.html import conditional_escape
from zambia.con_settings import *
from zambia.models import Track, Type, Division, PubStatus, LanguageStatus, Session, KidsCategory, RoomSet, SessionStatus
import datetime

def hms2time(hms):
    h = int(hms[0:2])
    m = int(hms[3:5])
    s = int(hms[6:8])
    return datetime.time(h, m, s)

def get_track_names(allow_all = False):
    ret = []
    if allow_all:
        ret.append({'name': 'All tracks', 'value': ''})
    for t in Track.objects.order_by('display_order'):
        ret.append({'name': t.trackname, 'value': t.trackname })
    return ret

def error_page(request, text):
    template = loader.get_template('zambia/error.html')
    context = { 'text': text }
    return HttpResponse(template.render(context, request))

def view_login(request, reason = None):
    if request.method == 'POST':
        username = request.POST['username']
        password = request.POST['password']
        user = authenticate(username=username, password=password)
        if user is not None:
            if user.is_active:
                login(request, user)
                if user.has_perm('zambia.staff'):
                    return redirect('StaffPage')
                if user.has_perm('zambia.participant'):
                    return redirect('welcome')
                if user.has_perm('zambia.public'):
                    return redirect('BrainstormWelcome')
                return error_page(request, "The logged in user has no permissions.")
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

@login_required
def welcome(request):
    return error_page(request, 'Welcome is not implemented yet.')

@login_required
def staff_page(request):
    return error_page(request, 'StaffPage is not implemented yet.')

@login_required
def brainstorm_welcome(request):
    template = loader.get_template('zambia/brainstorm_welcome.html')
    context = {
        'CON_NAME':         CON_NAME,
        'BRAINSTORM_EMAIL': BRAINSTORM_EMAIL,
        'PROGRAM_EMAIL':    PROGRAM_EMAIL,
        'can_brainstorm':   request.user.has_perm('zambia.brainstorm'),
        'is_participant':   request.user.has_perm('zambia.participant')
    }
    return HttpResponse(template.render(context, request))

@login_required
def brainstorm_create_session(request):
    error = None
    name = None
    email = None
    title = ''
    pgd = ''
    notesforprog = ''

    def_type = "Panel"
    def_divisionid = "Programming"
    def_roomset = 'Unspecified'
    def_languagestatusid = "English"
    def_pubstatusid = "Public"
    def_pubno = ''
    def_duration = '01:15:00'
    def_atten = ''
    def_kids = 'Kids Welcome'
    def_status = 'Brainstorm'

    if request.method == 'POST':
        name = request.POST['name']
        email = request.POST['email']
        track = request.POST['track']
        title = request.POST['title']
        pgd = request.POST['progguiddesc']
        notesforprog = request.POST['notesforprog']

        pubsno = request.POST['pubno']
        if pubsno == '':
            pubsno = None

        typename = request.POST['type']
        divisionname = request.POST['divisionid']
        pubstatusname = request.POST['pubstatusid']
        languagestatusname = request.POST['languagestatusid']
        kidscatname = request.POST['kids']
        roomsetname = request.POST['roomset']
        statusname = request.POST['status']
        duration = request.POST['duration']

        trid = Track.objects.get(trackname=track)
        tyid = Type.objects.get(typename = typename)
        dvid = Division.objects.get(divisionname = divisionname)
        psid = PubStatus.objects.get(pubstatusname = pubstatusname)
        laid = LanguageStatus.objects.get(languagestatusname = languagestatusname)
        kcid = KidsCategory.objects.get(kidscatname = kidscatname)
        rsid = RoomSet.objects.get(roomsetname = roomsetname)
        stid = SessionStatus.objects.get(statusname = statusname)
        duration = hms2time(duration)
        s = Session(track = trid, type = tyid, division = dvid, pubstatus = psid, languagestatus = laid, kidscat = kcid, roomset = rsid, status = stid, title = title, progguiddesc = pgd, notesforprog = notesforprog, pubsno = pubsno, invitedguest = 0, warnings = 0, duration = duration, ts = datetime.datetime.now())
        s.save()

    template = loader.get_template('zambia/brainstorm_create.html')
    tlist = get_track_names(False)
    context = {
        'name': name,
        'email': email,
        'tracks': tlist,
        'title': title,
        'progguidedesc': pgd,
        'notesforprog': notesforprog,

        'type': def_type,
        'divisionid': def_divisionid,
        'roomset': def_roomset,
        'languagestatusid': def_languagestatusid,
        'pubstatusid': def_pubstatusid,
        'pubno': def_pubno,
        'duration': def_duration,
        'atten': def_atten,
        'kids': def_kids,
        'status': def_status,

        'error': error,
    }
    return HttpResponse(template.render(context, request))

def session_row(s, room, time):
    return {
        'id': s.sessionid,
        'trackname': s.track.trackname,
        'typename': '',
        'title': s.title,
        'duration': s.duration.isoformat(),
        'roomname': room,
        'starttime': time,
        'estatten': s.estatten,
        'progguiddesc': s.progguiddesc,
        'perspartinfo': s.persppartinfo
    }

@login_required
def brainstorm_report(request, qtype = 'All', track = None, title = None):
    template = loader.get_template('zambia/brainstorm_report.html')
    kw = {}
    error = None
    if qtype == 'All':
        caption = 'All Sessions'
        valid_statuses = ('Edit Me', 'Brainstorm', 'Vetted', 'Assigned', 'Scheduled')
        text = """<p>This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</p>
<p>We are in the process of sorting through these suggestions:
combining duplicates;
splitting big ones into pieces;
checking general feasability;
finding needed people to present;
looking for an appropiate time and location;
rewriting for clarity and proper english;
and hoping to find a time machine so we can do it all.</p>"""
    elif qtype == 'Unseen':
        caption = 'Unseen Sessions'
        valid_statuses = ('Brainstorm', )
        text = "<p> If an idea is on this page, there is a good chance we have not yet seen it. So, please wear your Peril Sensitive Sunglasses while reading. We do."
    elif qtype == 'Reviewed':
        caption = 'Reviewed Sessions'
        valid_statuses = ('Edit Me', 'Vetted', 'Assigned', 'Scheduled')
        text = """"<p> We've seen these. They have varying degrees of merit. We have or will sort through these suggestions:
combining duplicates;
splitting big ones into pieces;
checking general feasability;
finding needed people to present;
looking for an appropiate time and location;
rewriting for clarity and proper english;
and hoping to find a time machine so we can do it all.</p>
<p> Note that ideas that we like and are pursuing further will stay on this list.  That is to make it easier to find the idea you suggested.</p>"""
    elif qtype == 'Likely':
        caption = 'Likely Sessions'
        valid_statuses = ('Vetted', 'Assigned', 'Scheduled')
        text = "<p>These ideas have made the first cut.  We like them and would like to see them happen.   Now to just find all the right people...</p>"
    elif qtype == 'Search':
        if track is not None and track != '':
            kw['track__trackname'] = track
        if title is not None and title != '':
            kw['title__icontains'] = title
        caption = 'Sessions'
        valid_statuses = ('Edit Me', 'Brainstorm', 'Vetted', 'Assigned', 'Scheduled')
        text = """<p This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</p>
<p>We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces;
checking general feasability; finding needed people to present; looking for an appropiate time and location;
rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</p>"""
    else:
        if qtype != 'Scheduled':
            error = 'Invalid query type, assuming Scheduled'
        caption = 'Scheduled Sessions'
        valid_statuses = ('Assigned', 'Scheduled')
        text = """<p> These ideas are highly likely to make it into the final schedule.
Things are looking good for them.
Please remember	events out of our control and last minute emergencies cause this to change!
No promises, but we are doing our best to have this happen.</p>"""

    kw['status__statusname__in'] = valid_statuses
    kw['invitedguest'] = 0
    slist = []
    for s in Session.objects.filter(**kw ).order_by('track__display_order', 'title'):
        added = False
        for t in s.schedule_set.all():
            added = True
            slist.append(session_row(s, conditional_escape(t.room.roomname), (CON_START_DATIM+datetime.timedelta(hours=t.starttime.hour, minutes = t.starttime.minute, seconds = t.starttime.second)).isoformat()))
        if not added:
            slist.append(session_row(s, '&nbsp;', '&nbsp;'))
    context = {
        'error': error,
        'caption': caption,
        'date': datetime.datetime.now().isoformat(),
        'showlinks': False,
        'text': text,
        'sessions': slist,
        'PROGRAM_EMAIL': PROGRAM_EMAIL,
    }
    return HttpResponse(template.render(context, request))

@login_required
def brainstorm_search_session(request):
    if not request.user.has_perm('zambia.brainstorm_search'):
        return error_page(request, "You don't have permission to search brainstorm sessions.")

    if request.method == 'POST':
        track = request.POST['track']
        title = request.POST['title']
        return brainstorm_report(request, 'Search', track, title)
    template = loader.get_template('zambia/brainstorm_search.html')
    context = {
        'tracks': get_track_names(True)
    }
    return HttpResponse(template.render(context, request))

