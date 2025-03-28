import{a8 as l}from"./app-Bv7sA21o.js";const m=(o,s,c)=>{const i=document.querySelector("body"),t=document.createElement("div");t.className="modal",t.setAttribute("id","modal-dialog"),t.innerHTML=`<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-type">
                <i class="bi bi-question-lg"></i>
            </div>
            <div class="modal-body">
                <div class="row gy-4 text-center justify-content-center">
                    <div class="col-12">
                        <div class="title">${o}</div>
                    </div>
                    <div class="col-12">
                        <div class="row gx-3 justify-content-center">
                            <div class="col-auto">
                                <button type="button" id="confirm" class="btn btn-save btn-primary">OK</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-save btn-outline-primary close-btn" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`,i.append(t);const d=document.getElementById("modal-dialog"),a=document.getElementById("confirm"),e=new l(d,{keyboard:!1});return a.addEventListener("click",()=>{e.hide(),s()}),d.addEventListener("hidden.bs.modal",n=>{n.target.remove(),e.dispose()}),e};export{m as d};
