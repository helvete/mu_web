####################################################
# ctags for vim
CTAGS_COMMON_EXCLUDES = \
	--exclude=*.vim \
	--exclude=log \
	--exclude=temp \
	--exclude=www \
	--exclude=*.js \
	--exclude=*.latte \
	--exclude=docs

# ctags
ctags::
	rm -f TAGS
	ctags --recurse \
		--totals=yes \
		$(CTAGS_COMMON_EXCLUDES) \
		.
