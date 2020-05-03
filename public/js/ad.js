$('#add-image').click(function(){
    // je récupére le numéro des futurs champs que je vais créer
    const index  = +$('#widget-counter').val() ;
   // console.log(index) ;

    // je récupére le prototype des entrés
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g,index) ;

    // console.log(tmpl) ;

    // j'inject ce code au sein de la div 
    $('#ad_images').append(tmpl) ;

    $('#widget-counter').val(index + 1)

    // je gére le button supprimer 

    handleDeleteButtons() ;
}) ;

   function handleDeleteButtons() {
       $('button[data-action="delete"]').click(function(){
           const target = this.dataset.target ;
           $(target).remove() ;
       }) ;
   }
   function updateCounter(){
       const count = +$('#ad_images div.form-group').length ;

       $('#widget-counter').val(count) ;

   }

   updateCounter() ;
   handleDeleteButtons() ;