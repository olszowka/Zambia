from django.contrib import admin

from .models import ReportCategory
admin.site.register(ReportCategory)

from .models import ReportType
admin.site.register(ReportType)

from .models import CategoryHasReport
admin.site.register(CategoryHasReport)

from .models import CongoDump
admin.site.register(CongoDump)

from .models import CustomText
admin.site.register(CustomText)

from .models import EmailCC
admin.site.register(EmailCC)

from .models import EmailFrom
admin.site.register(EmailFrom)

from .models import EmailQueue
admin.site.register(EmailQueue)

from .models import EmailTo
admin.site.register(EmailTo)

from .models import Feature
admin.site.register(Feature)

from .models import BioEditStatus
admin.site.register(BioEditStatus)

from .models import Participant
admin.site.register(Participant)

from .models import ParticipantAvailability
admin.site.register(ParticipantAvailability)

from .models import ParticipantAvailabilityDay
admin.site.register(ParticipantAvailabilityDay)

from .models import ParticipantAvailabilityTime
admin.site.register(ParticipantAvailabilityTime)

from .models import Credential
admin.site.register(Credential)

from .models import ParticipantHasCredential
admin.site.register(ParticipantHasCredential)

from .models import Role
admin.site.register(Role)

from .models import ParticipantHasRole
admin.site.register(ParticipantHasRole)

from .models import ParticipantInterest
admin.site.register(ParticipantInterest)

from .models import Track
admin.site.register(Track)

from .models import Type
admin.site.register(Type)

from .models import Division
admin.site.register(Division)

from .models import PubStatus
admin.site.register(PubStatus)

from .models import LanguageStatus
admin.site.register(LanguageStatus)

from .models import KidsCategory
admin.site.register(KidsCategory)

from .models import RoomSet
admin.site.register(RoomSet)

from .models import SessionStatus
admin.site.register(SessionStatus)

from .models import Session
admin.site.register(Session)

from .models import ParticipantOnSession
admin.site.register(ParticipantOnSession)

from .models import ParticipantOnSessionHistory
admin.site.register(ParticipantOnSessionHistory)

from .models import ParticipantSessionInterest
admin.site.register(ParticipantSessionInterest)

from .models import ParticipantSuggestion
admin.site.register(ParticipantSuggestion)

from .models import PatchLog
admin.site.register(PatchLog)

from .models import PermissionAtom
admin.site.register(PermissionAtom)

from .models import Phase
admin.site.register(Phase)

from .models import PermissionRole
admin.site.register(PermissionRole)

from .models import Permission
admin.site.register(Permission)

from .models import PreviousCon
admin.site.register(PreviousCon)

from .models import PreviousConTrack
admin.site.register(PreviousConTrack)

from .models import PreviousParticipant
admin.site.register(PreviousParticipant)

from .models import PreviousSession
admin.site.register(PreviousSession)

from .models import PubCharacteristic
admin.site.register(PubCharacteristic)

from .models import RegType
admin.site.register(RegType)

from .models import ReportQuery
admin.site.register(ReportQuery)

from .models import Room
admin.site.register(Room)

from .models import RoomHasSet
admin.site.register(RoomHasSet)

from .models import Schedule
admin.site.register(Schedule)

from .models import Service
admin.site.register(Service)

from .models import SessionEditCode
admin.site.register(SessionEditCode)

from .models import SessionEditHistory
admin.site.register(SessionEditHistory)

from .models import SessionHasFeature
admin.site.register(SessionHasFeature)

from .models import SessionHasPubChar
admin.site.register(SessionHasPubChar)

from .models import SessionHasService
admin.site.register(SessionHasService)

from .models import TimeSlot
admin.site.register(TimeSlot)

from .models import TrackCompatibility
admin.site.register(TrackCompatibility)

from .models import UserHasPermissionRole
admin.site.register(UserHasPermissionRole)

