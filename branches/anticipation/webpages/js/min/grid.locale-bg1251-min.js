(function(a){a.jgrid={};a.jgrid.defaults={recordtext:"�����(�)",loadtext:"��������...",pgtext:"��"};a.jgrid.search={caption:"�������...",Find:"������",Reset:"�������",odata:["�����","��������","��-�����","��-����� ���=","��-������","��-������ ��� =","������� �","�������� �","�������"]};a.jgrid.edit={addCaption:"��� �����",editCaption:"�������� �����",bSubmit:"������",bCancel:"�����",bClose:"�������",processData:"���������...",msg:{required:"������ � ������������",number:"�������� ������� �����!",minValue:"���������� ������ �� � ��-������ ��� ����� ��",maxValue:"���������� ������ �� � ��-����� ��� ����� ��",email:"�� � ������� e-mail �����",integer:"�������� ������� ���� �����",date:"�������� ������� ����"}};a.jgrid.del={caption:"���������",msg:"�� ������ �� �������� �����?",bSubmit:"������",bCancel:"�����",processData:"���������..."};a.jgrid.nav={edittext:" ",edittitle:"�������� �� ������ �����",addtext:" ",addtitle:"�������� �� ��� �����",deltext:" ",deltitle:"��������� �� ������ �����",searchtext:" ",searchtitle:"������� �����(�) ",refreshtext:"",refreshtitle:"������ �������",alertcap:"��������������",alerttext:"����, �������� �����"};a.jgrid.col={caption:"������",bSubmit:"�����",bCancel:"�����"};a.jgrid.errors={errcap:"������",nourl:"���� ������� URL �����",norecords:"���� ����� �� ���������",model:"������� �� ����������� �� �������!"};a.jgrid.formatter={integer:{thousandsSeparator:" ",defaulValue:0},number:{decimalSeparator:".",thousandsSeparator:" ",decimalPlaces:2,defaultValue:0},currency:{decimalSeparator:".",thousandsSeparator:" ",decimalPlaces:2,prefix:"",suffix:" ??.",defaultValue:0},date:{dayNames:["���","���","��","��","���","���","���","������","����������","�������","�����","���������","�����","������"],monthNames:["��","���","����","���","���","���","���","���","���","���","����","���","������","��������","����","�����","���","���","���","������","���������","��������","�������","��������"],AmPm:["","","",""],S:function(b){if(b==7||b==8||b==27||b==28){return"��"}return["��","��","��"][Math.min((b-1)%10,2)]},srcformat:"Y-m-d",newformat:"d/m/Y",masks:{ISO8601Long:"Y-m-d H:i:s",ISO8601Short:"Y-m-d",ShortDate:"n/j/Y",LongDate:"l, F d, Y",FullDateTime:"l, F d, Y g:i:s A",MonthDay:"F d",ShortTime:"g:i A",LongTime:"g:i:s A",SortableDateTime:"Y-m-d\\TH:i:s",UniversalSortableDateTime:"Y-m-d H:i:sO",YearMonth:"F, Y"},reformatAfterEdit:false},baseLinkUrl:"",showAction:"show"}})(jQuery);