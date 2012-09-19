(setq org-publish-project-alist
      '(("Zambia-html"
	 :base-directory "~/nelaroot/www/FFF-NE-38/Documentation/Source"
	 :base-extension "org"
	 :publishing-directory "~/nelaroot/www/FFF-NE-38/Documentation"
	 :publishing-function org-publish-org-to-html
	 :preserve-breaks t
	 :sub-superscript nil
	 :timestamps nil
	 :timestamp nil
	 :creator-info nil
	 :headline-levels 3
	 :section-numbers t
	 :fixed-width nil
	 :tables t
	 :tables-auto-headline t
	 :special-strings t
	 :todo-keywords nil
	 :tasks nil
	 :tags t
	 :emphasize t
	 :author "Percy"
	 :email "NELA.Percy@gmail.com"
	 :author-info t
	 :email-info t
	 :skip-before-1st-heading nil
	 :drawers t
	 :footnotes t
	 :priority t
	 :table-of-contents t
	 :auto-sitemap t
	 :sitemap-title "Site Map"
	 :sitemap-sort-folders first
	 :style "<link rel=\"stylesheet\" href=\"../webpages/Common.css\" type=\"text/css\"/>"
	 :html-preamble t)

	("Zambia-pdf"
	 :base-directory "~/nelaroot/www/FFF-NE-38/Documentation/Source"
	 :base-extension "org"
	 :publishing-directory "~/nelaroot/www/FFF-NE-38/Documentation/PDF"
	 :publishing-function org-publish-org-to-pdf
	 :preserve-breaks t
	 :sub-superscript nil
	 :timestamps nil
	 :timestamp nil
	 :creator-info nil
	 :headline-levels 3
	 :section-numbers t
	 :fixed-width nil
	 :tables t
	 :tables-auto-headline t
	 :special-strings t
	 :todo-keywords nil
	 :tasks nil
	 :tags t
	 :emphasize t
	 :author "Percy"
	 :email "NELA.Percy@gmail.com"
	 :author-info t
	 :email-info t
	 :skip-before-1st-heading nil
	 :drawers t
	 :footnotes t
	 :priority t
	 :auto-sitemap t
	 :sitemap-title "Site Map"
	 :sitemap-sort-folders first
	 :table-of-contents t)

	("Zambia-images"
	 :base-directory "~/nelaroot/www/FFF-NE-38/Documentation/Source/Images"
	 :base-extension "jpg\\|gif\\|png"
	 :publishing-directory "~/nelaroot/www/FFF-NE-38/Documentation/Images"
	 :publishing-function org-publish-attachment)

	("Zambia-other"
	 :base-directory "~/nelaroot/www/FFF-NE-38/Documentation/Source"
	 :base-extension "css"
	 :publishing-directory "~/nelaroot/www/FFF-NE-38/Documentation"
	 :publishing-function org-publish-attachment)

	("Zambia" :components ("Zambia-html" "Zambia-pdf" "Zambia-images" "Zambia-other"))))
