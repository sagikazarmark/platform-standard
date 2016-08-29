set :application, "webplates_platform"
set :repo_url, "git@github.com:webplates/platform-standard.git"
set :branch, ENV["BRANCH"] || "master"

# Symfony settings
set :session_path, fetch(:var_path) + "/sessions"
set :controllers_to_clear, ["app_*.php", "config.php"]


# Banner
set :banner_path, fetch(:deploy_path) + "/banner.txt"
set :banner_options, {
    :pause => false,
    :force => true
}


# Shared content
set :linked_files, [fetch(:app_config_path) + "/parameters.yml"]
set :linked_dirs, [
    fetch(:log_path),
    fetch(:session_path),
    fetch(:web_path) + "/uploads",
    fetch(:var_path) + "/attachments"
]
set :copy_files, [
    "vendor/"
]


# Deploy hooks
namespace :deploy do
    after :starting, "composer:install_executable"
end


# System settings
set :ssh_options, forward_agent: true


# Airbrussh settings
set :format_options, log_file: "var/logs/capistrano.log"
