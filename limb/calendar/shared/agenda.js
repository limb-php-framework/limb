//////////////////// Agenda file for CalendarXP 6.0 /////////////////
// This file is totally configurable. You may remove all the comments in this file to shrink the download size.
/////////////////////////////////////////////////////////////////////

//////////////////// Define agenda events ///////////////////////////
// Usage -- fAddEvent(year, month, day, message, action, bgcolor, fgcolor, bgimg, boxit, html);
// Notice:
// 1. The (year,month,day) identifies the date of the agenda.
// 2. In the action part you can use any javascript statement, or use " " for doing nothing.
// 3. Assign "null" value to action will result in a line-through effect(can't be selected).
// 4. html is the HTML string to be shown inside the agenda cell, usually an <img> tag.
// 5. fgcolor is the font color for the specific date.
// 6. bgimg is the url of the background image file for the specific date.
// 7. boxit is a boolean that enables the box effect using the bgcolor when set to true.
/////////////////////////////////////////////////////////////////////
fAddEvent(2002,10,18," October 18, 2002 \n \"PopCalendarXP 6.0\" unleashed! ","alert('Here is the current size of the calendar - \"width='+gfSelf.width+' height='+gfSelf.height+'\"');","skyblue","dodgerblue");
fAddEvent(2002,12,2," Your comments're of vital importance. ","popup('mailto:Popcal@calendarxp.net?subject=Comments on PopCalendarXP')","skyblue","dodgerblue",null,true);


fAddEvent(2002,9,23,"Hello World!","alert('Hello World!')","skyblue","dodgerblue");


///////////// Dynamic holiday calculations /////////////////////////
// This function provides you a flexible way to make holidays of your own. It must be defined!!
// It will be called whenever the calendar engine needs the agenda info of a specific date, and the date is passed in as (y,m,d);
// With the date in hand, just do whatever you want to check to validate whether it is a desired holiday;
// Finally you should return an agenda array like [message, action, bgcolor, fgcolor, bgimg, boxit, html] to tell the engine how to render it.
////////////////////////////////////////////////////////////////////
function fHoliday(y,m,d) {
	var r=fGetEvent(y,m,d); // get agenda event.
	if (r) return r;	// ignore the following holiday checking if the date has already been set by the above addEvent functions. Of course you can write your own code to merge them instead of just ignoring.

	// you may have sophisticated holiday calculation set here, following are only simple examples.
	if (m==1&&d==1)
		r=[" Jan 1, "+y+" \n Happy New Year! ","","skyblue","red"];
	else if (m==12&&d==25)
		r=[" Dec 25, "+y+" \n Merry X'mas! ",null,"skyblue","red"];	// show a line-through effect
	else if (m==5&&d>20) {
		var date=getDateByDOW(y,5,5,1);	// Memorial day is the last Monday of May
		if (d==date) r=["May "+d+", "+y+"  Memorial Day ",gsAction,"skyblue","red"];	// use default action
	}

	return r;	// if r is null, the engine will just render it as a normal day.
}


// -- You may also put your self-defined functions here if required, like the following two which are used in the above examples. --
function popup(url,framename) {	// popup an url in the designated window, you may delete it if no need.
	var w=parent.open(url,framename,"top=200,left=200,width=400,height=200,scrollbars=1,resizable=1");
	if (w&&url.split(":")[0]=="mailto") w.close();
	else if (w&&!framename) w.focus();
}

function getDateByDOW(y,m,q,n) { // return the actual date of the q-th n-day in the specific month (y,m), you may delete it if no need.
// n: 0 - Sunday, 1 - Monday ... 6 - Saturday
// q: 1 - 5 ( 5 denotes the last n-day )
	var dom=new Date(y,m-1,1).getDay();
	var d=7*q-6+n-dom;
	if (dom>n) d+=7;
	if (d>fGetDays(y)[m]) d-=7;
	return d;
}

