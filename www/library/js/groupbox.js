function GroupBox(nomParent){
			this.CreatGrpBox=function(){
			req=document.getElementById("requette").selectedItem.value;
			//alert(req);
			
			if((req=="GetAllTags")||(req=="GetAllPosts")||(req=="GetAllBundles")){
		 	    t=document.getElementById(nomParent);
		        boxb=document.createElement("groupbox");
				if(t.hasChildNodes()){
					dernier=t.lastChild;
					t.removeChild(dernier);
				}
			}
		 	if(req=="GetRecentPosts"){
		 	    t=document.getElementById(nomParent);
		        boxb=document.createElement("groupbox");
				if(t.hasChildNodes()){
					dernier=t.lastChild;
					t.removeChild(dernier);
				}
				boxb.setAttribute("flex",1);
				//creation de caption 
				
				cap=document.createElement("caption");
				cap.setAttribute("label","Parametres");
				boxb.appendChild(cap);
				
				//creation de label
				
				lab1=document.createElement("label");
				lab1.setAttribute("value","tag");
				boxb.appendChild(lab1);
				
				//creation de textbox
				
				txtbox1=document.createElement("textbox");
				txtbox1.setAttribute("value","");
				txtbox1.setAttribute("id","id-tag");
				boxb.appendChild(txtbox1);
				
				//creation de label
				
				lab2=document.createElement("label");
				lab2.setAttribute("value","count");
				boxb.appendChild(lab2);
				
				//creation de textbox
				
				txtbox2=document.createElement("textbox");
				txtbox2.setAttribute("value","");
				txtbox2.setAttribute("id","id-count");
				boxb.appendChild(txtbox2);
				t.appendChild(boxb);
				
		 	 }else
		 		
		 		if(req=="GetPosts"){
			 		t=document.getElementById(nomParent);
			        
			        boxb=document.createElement("groupbox");
					if(t.hasChildNodes()){
					  dernier=t.lastChild;
					  t.removeChild(dernier);
					  
					}
					  boxb.setAttribute("flex",1);
						//creation de caption 
						
						cap=document.createElement("caption");
						cap.setAttribute("label","Parametres requete");
						boxb.appendChild(cap);
						
						//creation de label
						
						lab1=document.createElement("label");
						lab1.setAttribute("value","tag");
						boxb.appendChild(lab1);
						
						//creation de textbox
						
						txtbox1=document.createElement("textbox");
						txtbox1.setAttribute("value","");
						txtbox1.setAttribute("id","id-tag");
						boxb.appendChild(txtbox1);
						
						//creation de label
						
						lab2=document.createElement("label");
						lab2.setAttribute("value","date");
						boxb.appendChild(lab2);
						
						//creation de textbox
						
						txtbox2=document.createElement("textbox");
						txtbox2.setAttribute("value","");
						txtbox2.setAttribute("id","id-date");
						boxb.appendChild(txtbox2);
				 		
				 		//creation de label
						
						lab3=document.createElement("label");
						lab3.setAttribute("value","url");
						boxb.appendChild(lab3);
						
						//creation de textbox
						
						txtbox3=document.createElement("textbox");
						txtbox3.setAttribute("value","");
						txtbox3.setAttribute("id","id-url");
						boxb.appendChild(txtbox3);
				 		
				 		
				 		t.appendChild(boxb);
		 		}
	
	
		}
}
