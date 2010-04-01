function jumpMenue( form ) {
    var newIndex = form.kw.selectedIndex;
    cururl = form.kw.options[ newIndex ].value;
    window.location.assign( cururl );
}

function bookerAction(mode,date)
{
    var urlBase = "/cal/booker/do/";
    var ndate = trim(date);
    var anode = dojo.byId("a"+ndate);
    var tdnode = dojo.byId("td"+ndate);


    switch (mode) {
        case "book":
            ZFurl = urlBase + "book/selectedDate/" + date;
            newHTML = '<a id="a'+ndate+'" href="#" onclick="bookerAction(\'unbook\',\''+date+'\');return false;" ><img src="/files/images/action_delete.png" />abw&auml;hlen<a/>';
            break;

        case "unbook":
            var ZFurl = urlBase + "unbook/selectedDate/" + date;
            newHTML = '<a id="a'+ndate+'" href="#" onclick="bookerAction(\'book\',\''+date+'\');return false;" ><img src="/files/images/action_check.png" />anw&auml;hlen<a/>';
            break;
    }
    
    dojo.anim(anode, {
        opacity: 0
    }, 500, null, function(){

        dojo.xhrGet({
            url: ZFurl,
            handleAs: "text"
        });
        
        tdnode.innerHTML = newHTML;
        dojo.anim(anode, {
            opacity: 100
        }, 500);
    });
    
}

function trim(s){
    s = s.replace(" ","");
    s = s.replace("-","");
    s = s.replace("-","");
    s = s.replace(":00:00","");

    return s;
}