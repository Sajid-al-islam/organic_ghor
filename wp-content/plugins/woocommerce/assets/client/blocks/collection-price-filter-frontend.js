(()=>{"use strict";const e=window.wc.__experimentalInteractivity,r=window.wc.priceFormat,t=e=>{const{minPrice:r=0,maxPrice:t=0,maxRange:i=0}=e,a=new URL(window.location.href),{searchParams:n}=a;return r>0?n.set("min_price",r.toString()):n.delete("min_price"),t<i?n.set("max_price",t.toString()):n.delete("max_price"),n.forEach(((e,r)=>{/query-[0-9]+-page/.test(r)&&n.delete(r)})),a.href},{state:i}=(0,e.store)("woocommerce/collection-price-filter",{state:{get rangeStyle(){const{minPrice:e=0,maxPrice:r=0,minRange:t=0,maxRange:a=0}=i;return[`--low: ${100*(e-t)/(a-t)}%`,`--high: ${100*(r-t)/(a-t)}%`].join(";")},get formattedMinPrice(){const{minPrice:e=0}=i;return(0,r.formatPrice)(e,(0,r.getCurrency)({minorUnit:0}))},get formattedMaxPrice(){const{maxPrice:e=0}=i;return(0,r.formatPrice)(e,(0,r.getCurrency)({minorUnit:0}))}},actions:{setMinPrice:e=>{const{minRange:r=0,maxPrice:t=0,maxRange:a=0}=i,n=parseFloat(e.target.value);i.minPrice=Math.min(Number.isNaN(n)?r:n,a-1),i.maxPrice=Math.max(t,i.minPrice+1)},setMaxPrice:e=>{const{minRange:r=0,minPrice:t=0,maxPrice:a=0,maxRange:n=0}=i,c=parseFloat(e.target.value);i.maxPrice=Math.max(Number.isNaN(c)?n:c,r+1),i.minPrice=Math.min(t,a-1)},updateProducts:()=>{(0,e.navigate)(t(i))},reset:()=>{const{maxRange:r=0}=i;i.minPrice=0,i.maxPrice=r,(0,e.navigate)(t(i))}}})})();