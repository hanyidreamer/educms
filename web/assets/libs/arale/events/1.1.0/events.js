define("arale/events/1.1.0/events",[],function(){function t(){}function e(t,e,r,n){var s;if(t)for(var o=0,i=t.length;i>o;o+=2){try{s=t[o].apply(t[o+1]||r,e)}catch(f){window.console&&console.error&&"[object Function]"===Object.prototype.toString.call(console.error)&&console.error(f.stack||f);continue}s===!1&&n.status&&(n.status=!1)}}var r=/\s+/;t.prototype.on=function(t,e,n){var s,o,i;if(!e)return this;for(s=this.__events||(this.__events={}),t=t.split(r);o=t.shift();)i=s[o]||(s[o]=[]),i.push(e,n);return this},t.prototype.off=function(t,e,s){var o,i,f,a;if(!(o=this.__events))return this;if(!(t||e||s))return delete this.__events,this;for(t=t?t.split(r):n(o);i=t.shift();)if(f=o[i])if(e||s)for(a=f.length-2;a>=0;a-=2)e&&f[a]!==e||s&&f[a+1]!==s||f.splice(a,2);else delete o[i];return this},t.prototype.trigger=function(t){var n,s,o,i,f,a,u=[],c={status:!0};if(!(n=this.__events))return this;for(t=t.split(r),f=1,a=arguments.length;a>f;f++)u[f-1]=arguments[f];for(;s=t.shift();)(o=n.all)&&(o=o.slice()),(i=n[s])&&(i=i.slice()),e(i,u,this,c),e(o,[s].concat(u),this,c);return c.status},t.mixTo=function(e){e=e.prototype||e;var r=t.prototype;for(var n in r)r.hasOwnProperty(n)&&(e[n]=r[n])};var n=Object.keys;return n||(n=function(t){var e=[];for(var r in t)t.hasOwnProperty(r)&&e.push(r);return e}),t});
