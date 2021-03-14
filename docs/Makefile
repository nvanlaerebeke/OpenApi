SOURCE_DIR='../OpenApi'
BUILD_DIR='./build/api'

.PHONY: build clean

clean:
	rm -rf $(BUILD_DIR)

# Make a macro to save re-typing recipies multiple times
build:
	cd $(SOURCE_DIR)
	# Update the config file, Remove sed crap
	sed -i.bak "s/activeVersion: '[0-9]\.[0-9]'/activeVersion: '$(VERSION)'/" templates/openapi/config.neon
	rm templates/openapi/config.neon.bak
	# Make the build output dir
	[ ! -d $(BUILD_DIR) ] && mkdir $(BUILD_DIR) || true
	# Run Apigen
	php apigen.php --source $(SOURCE_DIR) \
		--debug \
		--config ./apigen.neon \
		--exclude $(SOURCE_DIR)/app/Config/database.php \
		--exclude $(SOURCE_DIR)/lib/Cake/Console/Command/AppShell.php \
		--exclude $(SOURCE_DIR)/lib/Cake/Test \
		--exclude $(SOURCE_DIR)/lib/Cake/Console/Templates \
		--destination $(BUILD_DIR)/$(VERSION) \
		--template-config ./templates/openapi/config.neon
	# Fix rewrites file to have a opening php tag at the start
	sed -i.bak '1i<?php' $(BUILD_DIR)/$(VERSION)/rewrite.php && rm $(BUILD_DIR)/$(VERSION)/rewrite.php.bak
