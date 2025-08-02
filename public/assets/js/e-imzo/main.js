
var uiShowMessage = function(message){
    alert(message);
}

var uiLoading = function(){
    var l = document.getElementById('message');
    l.innerHTML = 'Yuklanmoqda ...';
    l.style.color = 'red';
}

var uiNotLoaded = function(e){
    var l = document.getElementById('message');
    l.innerHTML = '';
    if (e) {
        wsError(e);
    } else {
        uiShowMessage(errorBrowserWS);
    }
}

var uiUpdateApp = function(){
    var l = document.getElementById('message');
    l.innerHTML = errorUpdateApp;
}

var uiAppLoad = function(){
    uiClearCombo();
    EIMZOClient.listAllUserKeys(function(o, i){
        var itemId = "itm-" + o.serialNumber + "-" + i;
        return itemId;
    },function(itemId, v){
        return uiCreateItem(itemId, v);
    },function(items, firstId){
        uiFillCombo(items);
        uiLoaded();
        uiComboSelect(firstId);
    },function(e, r){
        if(e){
            uiShowMessage(errorCAPIWS + " : " + e);
        } else {
            console.log(r);
        }
    });
    EIMZOClient.idCardIsPLuggedIn(function(yes){
        document.getElementById('plugged').innerHTML = yes ? 'ulangan': 'ulanmagan';
    },function(e, r){
        if(e){
            uiShowMessage(errorCAPIWS + " : " + e);
        } else {
            console.log(r);
        }
    })
}

var uiComboSelect = function(itm){
    if(itm){
        var id = document.getElementById(itm);
        id.setAttribute('selected','true');
    }
}

var cbChanged = function(c){
    document.getElementById('keyId').innerHTML = '';
}

var uiClearCombo = function(){
    var combo = document.testform.key;
    combo.length = 0;
}

var uiFillCombo = function(items){
    var combo = document.testform.key;
    for (var itm in items) {
        combo.append(items[itm]);
    }
}

var uiLoaded = function(){
    var l = document.getElementById('message');
    l.innerHTML = '';
}

var uiCreateItem = function (itmkey, vo) {
    var now = new Date();
    vo.expired = dates.compare(now, vo.validTo) > 0;
    var itm = document.createElement("option");
    itm.value = itmkey;
    itm.text = vo.CN;
    if (!vo.expired) {

    } else {
        itm.style.color = 'gray';
        itm.text = itm.text + ' (срок истек)';
    }
    itm.setAttribute('vo',JSON.stringify(vo));
    itm.setAttribute('id',itmkey);
    return itm;
}

var keyType_changed = function(){
    var keyType = document.testform.keyType.value;
    document.getElementById('signButton').innerHTML = keyType==="pfx" ? "ERI kaliti bilan kirish" : "ID-karta kaliti bilan kirish";
};

keyType_changed();

var uiShowProgress = function(){
    var l = document.getElementById('progress');
    l.innerHTML = 'Imzolanish davom etmoqda, kuting.';
    l.style.color = 'green';
};

var uiHideProgress = function(){
    var l = document.getElementById('progress');
    l.innerHTML = '';
};

signin = function () {
    uiShowProgress();

    getChallenge(function(challenge){
        var keyType = document.testform.keyType.value;
        if(keyType==="idcard"){
            var keyId = "idcard";

            auth(keyId, challenge, function(redirect){
                window.location.href = redirect;
                uiShowProgress();
            });

        } else {
            var itm = document.testform.key.value;
            if (itm) {
                var id = document.getElementById(itm);
                var vo = JSON.parse(id.getAttribute('vo'));

                EIMZOClient.loadKey(vo, function(id){
                    var keyId = id;

                    auth(keyId, challenge, function(redirect){
                        window.location.href = redirect;
                        uiShowProgress();
                    });

                }, uiHandleError);
            } else {
                uiHideProgress();
            }
        }
    });
};

getChallenge = function (callback){
    microAjax('/frontend/challenge?_uc='+(Date.now() + "_" + Math.random()), function (data, s) {
        if(s.status != 200){
            uiShowMessage(s.status + " - " + s.statusText);
            return;
        }
        try {
            var data = JSON.parse(data);
            if (data.status != 1) {
                uiShowMessage(data.status + " - " + data.message);
                return;
            }
            callback(data.challenge);
        } catch (e) {
            uiShowMessage(s.status + " - " + s.statusText + ": " + e);
        }
    });
}

auth = function (keyId, challenge, callback){
    EIMZOClient.createPkcs7(keyId, challenge, null, function(pkcs7){
        microAjax('backend/auth', function (data, s) {
            uiHideProgress();
            if(s.status != 200){
                uiShowMessage(s.status + " - " + s.statusText);
                return;
            }
            try {
                var data = JSON.parse(data);
                if (data.status != 1) {
                    uiShowMessage(data.status + " - " + data.message);
                    return;
                }
                redirectUrl = data.redirect;
                callback( redirectUrl );
            } catch (e) {
                uiShowMessage(s.status + " - " + s.statusText + ": " + e);
            }

        }, 'keyId=' + encodeURIComponent(keyId) + '&pkcs7=' + encodeURIComponent(pkcs7));
    }, uiHandleError, false);
}

window.onload = AppLoad;
