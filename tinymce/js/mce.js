if(typeof window.$=="undefined"){window.$=jQuery}if(typeof window.mq=="undefined"){window.mq={}}(function(){var A;A=mq.mce={generateShortcode:function(C,B,E){var D="["+C;jQuery.each(B,function(F,G){if(G!=""){D+=" "+F+'="'+G+'"'}});if(E){D+="]"+E+"[/"+C+"]"}else{D+="/]"}return D.replace("&center","&amp;center")},getShortcodes:function(D){var B=tinyMCE.activeEditor,F,C,H=[],E=/\[([^\]]+)\](?:([^[]+)\[\/\w+\])?/g,G;if(B&&!B.isHidden()){F=B.getContent();while(C=E.exec(F)){G=this.parseShortcode(C[0]);G.shortcode=C[0];if(G.attributes.key===D){return G}H.push(G)}}if(D){return null}else{return H}},parseShortcode:function(F){if(!F){return }var E=/\[([^\]]+)\](?:([^[]+)\[\/\w+\])?/,D,G,C,B,H;if(D=E.exec(F)){G=D[1];H=D[2];E=/^(\w+)/;if(D=E.exec(G)){C=D[1];B={};E=/\s+(\w+)="([^"]+)"/g;while(D=E.exec(G)){B[D[1]]=D[2]}return{name:C,attributes:B,text:H}}}},insertContent:function(D){var C=tinyMCE.activeEditor;if(C&&!C.isHidden()){var B=this.parseShortcode(D),E=this.getShortcodes(B.attributes.key);if(E&&E.attributes.key==B.attributes.key){D=C.getContent().replace(E.shortcode,D);C.setContent(D)}else{C.execCommand("mceInsertContent",false,D)}}else{C=edCanvas;if(typeof edInsertContent=="function"){edInsertContent(C,D)}else{$(C).val($(C).val()+shortcode)}}},getSelectedContent:function(){var B=tinyMCE.activeEditor,D,C,E;if(B&&!B.isHidden()){E=B.selection.getContent()}else{B=edCanvas;if((D=B.selectionStart)||(D==0)){C=B.selectionEnd;if(D!=C){E=B.value.substring(D,C)}}}return E},getExistingMapsHTML:function(){var E=this.getShortcodes(),B,D=[],C;while(B=E.shift()){C=B.text||"Untitled Map";D.push('<li><a href="#" onclick="mq.mce.dialog.openMap(\''+B.attributes.key+"'); return false;\">"+C+"</a></li>")}return"<ol>"+D.join("\n")+"</ol>"}}})();