(function ($, Drupal) {

Drupal.behaviors.alloyBlogCategories = {
  attach: function(context, settings) {
    var $categories = $('[data-drupal-selector=edit-field-categories]', context),
      $blog = $('[data-drupal-selector=edit-field-blog]', context)
      ;
    function filterCategories() {
      var blog_id = $blog.val();
      $categories.find('input').each(function() {
        // Show the category if there is a blog id, and the category is assigned to the blog id.
        var $this = $(this), toggle = blog_id != '_none' && !!settings.alloy_blog_categories[blog_id] && settings.alloy_blog_categories[blog_id].includes($(this).val());
        $this.closest('.form-item').toggle(toggle);
        if (!toggle) {
          $this.prop('checked', false);
        }
      });
    }
    $blog.once('alloyBlogCategories').change(function() {
      filterCategories();
    });

    filterCategories();
  }
};

})(jQuery, Drupal);