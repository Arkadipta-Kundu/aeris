# Contributing to Aeris

We love your input! We want to make contributing to Aeris as easy and transparent as possible, whether it's:

- Reporting a bug
- Discussing the current state of the code
- Submitting a fix
- Proposing new features
- Becoming a maintainer

## Development Process

We use GitHub to host code, to track issues and feature requests, as well as accept pull requests.

## Pull Requests

Pull requests are the best way to propose changes to the codebase. We actively welcome your pull requests:

1. Fork the repo and create your branch from `main`.
2. If you've added code that should be tested, add tests.
3. If you've changed APIs, update the documentation.
4. Ensure the test suite passes.
5. Make sure your code lints.
6. Issue that pull request!

## Any contributions you make will be under the MIT Software License

In short, when you submit code changes, your submissions are understood to be under the same [MIT License](http://choosealicense.com/licenses/mit/) that covers the project. Feel free to contact the maintainers if that's a concern.

## Report bugs using GitHub's [issue tracker](https://github.com/yourusername/aeris/issues)

We use GitHub issues to track public bugs. Report a bug by [opening a new issue](https://github.com/yourusername/aeris/issues/new).

**Great Bug Reports** tend to have:

- A quick summary and/or background
- Steps to reproduce
  - Be specific!
  - Give sample code if you can
- What you expected would happen
- What actually happens
- Notes (possibly including why you think this might be happening, or stuff you tried that didn't work)

## Use a Consistent Coding Style

### PHP Code Style

- Use 4 spaces for indentation
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Use type hints where appropriate

### Frontend Code Style

- Use 2 spaces for HTML/CSS indentation
- Use meaningful class names
- Follow TailwindCSS conventions
- Use semantic HTML elements

### Database

- Use snake_case for table and column names
- Include proper foreign key constraints
- Use appropriate data types
- Add indexes for performance

## Code of Conduct

### Our Pledge

We as members, contributors, and leaders pledge to make participation in our community a harassment-free experience for everyone, regardless of age, body size, visible or invisible disability, ethnicity, sex characteristics, gender identity and expression, level of experience, education, socio-economic status, nationality, personal appearance, race, religion, or sexual identity and orientation.

### Our Standards

Examples of behavior that contributes to a positive environment:

- Using welcoming and inclusive language
- Being respectful of differing viewpoints and experiences
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

### Enforcement

Instances of abusive, harassing, or otherwise unacceptable behavior may be reported to the community leaders responsible for enforcement. All complaints will be reviewed and investigated promptly and fairly.

## Getting Started

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/yourusername/aeris.git
   cd aeris
   ```
3. **Create a branch** for your feature:
   ```bash
   git checkout -b feature/amazing-feature
   ```
4. **Make your changes**
5. **Test your changes** thoroughly
6. **Commit your changes**:
   ```bash
   git commit -m "Add amazing feature"
   ```
7. **Push to your fork**:
   ```bash
   git push origin feature/amazing-feature
   ```
8. **Create a Pull Request** on GitHub

## Development Setup

1. Set up a local web server (Apache/Nginx)
2. Install PHP 7.4+ and MySQL 5.7+
3. Clone the repository
4. Copy `config/database.php.example` to `config/database.php`
5. Configure your database settings
6. Import the database schema from `database/schema.sql`
7. Start developing!

## Areas for Contribution

- **Bug fixes** - Check the issues for bugs to fix
- **Features** - Look for feature requests or propose new ones
- **Documentation** - Improve existing docs or write new ones
- **Testing** - Add unit tests or integration tests
- **Performance** - Optimize queries and code
- **Security** - Security reviews and improvements
- **UI/UX** - Interface improvements and accessibility

## Questions?

Feel free to open an issue with your question or reach out to the maintainers.

Thank you for contributing! 🎉
