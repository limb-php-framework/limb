/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

Limb.namespace('Limb.md5');

//MD5 stuff

Limb.md5.hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
Limb.md5.b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
Limb.md5.chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

Limb.md5.hex_md5 = function (s){ return Limb.md5.binl2hex(Limb.md5.core_md5(Limb.md5.str2binl(s), s.length * Limb.md5.chrsz));}
Limb.md5.str_md5 = function (s){ return Limb.md5.binl2str(Limb.md5.core_md5(Limb.md5.str2binl(s), s.length * Limb.md5.chrsz));}

Limb.md5.core_md5 = function (x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;

  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;

    a = Limb.md5.ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = Limb.md5.ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = Limb.md5.ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = Limb.md5.ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = Limb.md5.ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = Limb.md5.ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = Limb.md5.ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = Limb.md5.ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = Limb.md5.ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = Limb.md5.ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = Limb.md5.ff(c, d, a, b, x[i+10], 17, -42063);
    b = Limb.md5.ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = Limb.md5.ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = Limb.md5.ff(d, a, b, c, x[i+13], 12, -40341101);
    c = Limb.md5.ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = Limb.md5.ff(b, c, d, a, x[i+15], 22,  1236535329);

    a = Limb.md5.gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = Limb.md5.gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = Limb.md5.gg(c, d, a, b, x[i+11], 14,  643717713);
    b = Limb.md5.gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = Limb.md5.gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = Limb.md5.gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = Limb.md5.gg(c, d, a, b, x[i+15], 14, -660478335);
    b = Limb.md5.gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = Limb.md5.gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = Limb.md5.gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = Limb.md5.gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = Limb.md5.gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = Limb.md5.gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = Limb.md5.gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = Limb.md5.gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = Limb.md5.gg(b, c, d, a, x[i+12], 20, -1926607734);

    a = Limb.md5.hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = Limb.md5.hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = Limb.md5.hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = Limb.md5.hh(b, c, d, a, x[i+14], 23, -35309556);
    a = Limb.md5.hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = Limb.md5.hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = Limb.md5.hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = Limb.md5.hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = Limb.md5.hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = Limb.md5.hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = Limb.md5.hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = Limb.md5.hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = Limb.md5.hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = Limb.md5.hh(d, a, b, c, x[i+12], 11, -421815835);
    c = Limb.md5.hh(c, d, a, b, x[i+15], 16,  530742520);
    b = Limb.md5.hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = Limb.md5.ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = Limb.md5.ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = Limb.md5.ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = Limb.md5.ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = Limb.md5.ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = Limb.md5.ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = Limb.md5.ii(c, d, a, b, x[i+10], 15, -1051523);
    b = Limb.md5.ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = Limb.md5.ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = Limb.md5.ii(d, a, b, c, x[i+15], 10, -30611744);
    c = Limb.md5.ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = Limb.md5.ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = Limb.md5.ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = Limb.md5.ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = Limb.md5.ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = Limb.md5.ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = Limb.md5.safe_add(a, olda);
    b = Limb.md5.safe_add(b, oldb);
    c = Limb.md5.safe_add(c, oldc);
    d = Limb.md5.safe_add(d, oldd);
  }
  return Array(a, b, c, d);

}

Limb.md5.cmn = function (q, a, b, x, s, t)
{
  return Limb.md5.safe_add(Limb.md5.bit_rol(Limb.md5.safe_add(Limb.md5.safe_add(a, q), Limb.md5.safe_add(x, t)), s),b);
}
Limb.md5.ff = function (a, b, c, d, x, s, t)
{
  return Limb.md5.cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
Limb.md5.gg = function (a, b, c, d, x, s, t)
{
  return Limb.md5.cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
Limb.md5.hh = function (a, b, c, d, x, s, t)
{
  return Limb.md5.cmn(b ^ c ^ d, a, b, x, s, t);
}
Limb.md5.ii = function (a, b, c, d, x, s, t)
{
  return Limb.md5.cmn(c ^ (b | (~d)), a, b, x, s, t);
}

Limb.md5.core_hmac = function (key, data)
{
  var bkey = Limb.md5.str2binl(key);
  if(bkey.length > 16) bkey = Limb.md5.core_md5(bkey, key.length * Limb.md5.chrsz);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = Limb.md5.core_md5(ipad.concat(Limb.md5.str2binl(data)), 512 + data.length * Limb.md5.chrsz);
  return Limb.md5.core_md5(opad.concat(hash), 512 + 128);
}

Limb.md5.safe_add = function (x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

Limb.md5.bit_rol = function (num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}

Limb.md5.str2binl = function (str)
{
  var bin = Array();
  var mask = (1 << Limb.md5.chrsz) - 1;
  for(var i = 0; i < str.length * Limb.md5.chrsz; i += Limb.md5.chrsz)
    bin[i>>5] |= (str.charCodeAt(i / Limb.md5.chrsz) & mask) << (i%32);
  return bin;
}

Limb.md5.binl2str = function (bin)
{
  var str = "";
  var mask = (1 << Limb.md5.chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += Limb.md5.chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
  return str;
}

Limb.md5.binl2hex = function (binarray)
{
  var hex_tab = Limb.md5.hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
  }
  return str;
}

Limb.md5.binl2b64 = function (binarray)
{
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3)
  {
    var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += Limb.md5.b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
}

