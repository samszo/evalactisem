
var grpBox= new GroupBox('box1');

function GroupBox(nomParent,req){

	
		this.boxParent=document.getElementById(nomParent);
		//verfier que le box existe
		
		
		    this.CreatGrpBox=function(){
			req=document.getElementById("selctreq").value;
			alert(req);
			
			
		 	if(req=="GetRecentPosts"){
		 	    
		 		
		 		t=document.getElementById("box1");
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
				boxb.appendChild(txtbox1);
				
				//creation de label
				
				lab2=document.createElement("label");
				lab2.setAttribute("value","count");
				boxb.appendChild(lab2);
				
				//creation de textbox
				
				txtbox2=document.createElement("textbox");
				txtbox2.setAttribute("value","");
				boxb.appendChild(txtbox2);
				t.appendChild(boxb);
				
		 	 }else
		 		
		 		if(req=="GetPosts"){
			 		t=document.getElementById("box1");
			        
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
						boxb.appendChild(txtbox1);
						
						//creation de label
						
						lab2=document.createElement("label");
						lab2.setAttribute("value","date");
						boxb.appendChild(lab2);
						
						//creation de textbox
						
						txtbox2=document.createElement("textbox");
						txtbox2.setAttribute("value","");
						boxb.appendChild(txtbox2);
				 		
				 		//creation de label
						
						lab3=document.createElement("label");
						lab3.setAttribute("value","url");
						boxb.appendChild(lab3);
						
						//creation de textbox
						
						txtbox3=document.createElement("textbox");
						txtbox3.setAttribute("value","");
						boxb.appendChild(txtbox3);
				 		
				 		
				 		t.appendChild(boxb);
		 		
		
		
		
		
		}
	
	
}
}
