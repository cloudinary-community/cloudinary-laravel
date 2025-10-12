🎃 Hacktoberfest 2025 Welcome! 🎃
Welcome to the Cloudinary Laravel SDK, and thank you for your interest in contributing during Hacktoberfest! We're excited to have you join our community of developers working to make media management in Laravel applications easier and more powerful.

About Cloudinary Laravel SDK:
The Cloudinary Laravel SDK is a package that provides seamless integration between Laravel applications and Cloudinary's media management platform. It offers a fluent API for uploading, optimizing, transforming, and delivering media files, along with Laravel-specific features like File Storage Driver implementation and Blade components.

Getting Started:
Before diving into contributions, please:

Read our CONTRIBUTING.md guide thoroughly
Browse existing issues to understand current priorities and challenges
Review open pull requests to avoid duplicate work
Set up your local development environment following the installation instructions
Familiarize yourself with Laravel 11+ and PHP 8.2+ requirements
Explore the codebase structure and existing components

What Makes a Quality Contribution:
We value meaningful contributions that improve the package and help the Laravel community. Here's what we're looking for:

Good Contributions Include:

Bug fixes with clear reproduction steps and test coverage
New features that align with Laravel conventions and Cloudinary capabilities
Documentation improvements with clear examples
Performance optimizations with benchmarks
Test coverage improvements for existing features
Blade component enhancements with practical use cases
Code quality improvements following PSR-12 standards
Examples and tutorials for common use cases

Please Avoid:

Trivial changes without context or value
Code that doesn't follow PSR-12 coding standards
Pull requests without accompanying tests
Breaking changes without prior discussion
Incomplete implementations or work-in-progress submissions
Changes that don't maintain Laravel 11+ compatibility
Modifications without updating relevant documentation

Contribution Guidelines:
Before You Start:

Check existing issues for tasks marked "good first issue" or "help wanted"
Comment on the issue to claim it and discuss your approach
For new features, open an issue first to discuss the proposal
Fork the repository and create a feature branch (not from main)
Ensure you have PHP 8.2+ and Laravel 11+ set up locally
Install dependencies with composer install

While Working:

Follow PSR-12 coding standards throughout your code
Write clear, meaningful commit messages
Keep commits atomic and focused on single changes
Add or update tests for all code changes
Run PHP Code Sniffer to check coding standards
Execute the test suite with composer test
Update documentation to reflect your changes
Ensure backward compatibility unless explicitly discussed

When Submitting:

Create a descriptive pull request with clear context
Link related issues using "Fixes #issue-number" syntax
Ensure all tests pass and coding standards are met
Update README.md and relevant documentation
Squash intermediate commits into meaningful ones
Be responsive to review feedback and questions
Keep your branch up to date with the main branch

Areas Where We Need Help:
Here are some areas where contributions would be especially valuable:

Documentation: Improve setup guides, add usage examples, create tutorials
Testing: Expand test coverage for File Storage Driver and Blade components
Bug Fixes: Check our issue tracker for reported bugs
Blade Components: Add new components or enhance existing ones (image, video, widget)
Feature Development: Implement new Cloudinary features in Laravel-friendly ways
Code Quality: Refactor code to improve maintainability
Performance: Optimize media handling and transformation operations
Examples: Create practical examples for common use cases
Laravel Integration: Improve integration with Laravel features and conventions

Development Environment Setup:

Ensure you have PHP 8.2+ installed
Install Composer if not already available
Fork and clone the repository
Run composer install to install dependencies
Set up a test Laravel application for integration testing
Configure Cloudinary credentials for testing
Run tests with composer test to verify setup

Code Quality Standards:

Follow PSR-12: Install PHP Code Sniffer and check your code
Write tests: All code changes must include appropriate tests
Document changes: Update README.md and inline documentation
Maintain compatibility: Ensure PHP 8.2+ and Laravel 11+ compatibility
Use meaningful names: Variables, methods, and classes should be self-documenting
Add comments: Explain complex logic and business rules
Handle errors: Include proper error handling and validation

Testing Requirements:
Your contribution must include tests:

Unit tests for new functionality
Integration tests for Laravel-specific features
Tests for edge cases and error conditions
Tests should be clear and maintainable
Run composer test before submitting
Ensure all existing tests still pass

Pull Request Process:

Create a feature branch with a descriptive name
Make your changes following the guidelines
Write or update tests for your changes
Run the test suite and ensure all tests pass
Check coding standards with PHP Code Sniffer
Update documentation as needed
Create a pull request with detailed description
Link any related issues
Wait for review and address feedback promptly

Communication:
We encourage you to:

Ask questions by opening an issue or in pull request comments
Share your approach before investing significant time
Be patient while waiting for reviews (maintainers are often volunteers)
Engage respectfully with other contributors and maintainers
Provide context and rationale for your changes

Hacktoberfest-Specific Guidelines:
Remember that Hacktoberfest is about learning and making meaningful contributions:

Quality over quantity: One well-tested feature is better than multiple rushed changes
Learn Laravel patterns: Use this opportunity to understand Laravel conventions
Follow standards: PSR-12 compliance is mandatory, not optional
Test thoroughly: No contribution without tests will be merged
Document well: Help others understand and use your contribution
Think long-term: Create changes that will benefit users for months to come

Project Structure Overview:

src/: Core package source code
tests/: Test suite
resources/views/components/: Blade components (image, video, widget)
config/: Configuration files
CloudinaryEngine.php: Main engine implementation with version constant

Blade Component Development:
When working on Blade components:

Follow existing component patterns
Add comprehensive property documentation
Include usage examples in documentation
Test components with various configurations
Consider accessibility and performance
Maintain consistent API with other components

Version Management:
If your contribution affects the package version:

Update CloudinaryEngine::PACKAGE_VERSION constant in src/CloudinaryEngine.php
Follow Semantic Versioning 2.0.0 principles
Discuss version impact in your pull request
Document breaking changes clearly

Common Pitfalls to Avoid:

Skipping tests: All contributions need test coverage
Ignoring PSR-12: Run Code Sniffer before submitting
Breaking compatibility: Maintain PHP 8.2+ and Laravel 11+ support
Poor commit messages: Write clear, descriptive commits
Working from main: Always create a feature branch
Incomplete documentation: Update all relevant documentation

Recognition:
All contributors are acknowledged in the project. We appreciate every meaningful contribution, regardless of size. Your work helps thousands of Laravel developers manage media more effectively.

Need Help?
If you have questions or need assistance:

Open an issue with your question
Check existing documentation and closed issues
Review the codebase for examples
Ask in your pull request comments

Code of Conduct:
We expect all contributors to:

Treat everyone with respect and professionalism
Provide constructive feedback
Welcome newcomers and help them learn
Focus on collaboration and improvement
Create an inclusive environment for all experience levels

Pull Request Checklist:
Before submitting your PR, ensure:

 Code follows PSR-12 coding standards
 All tests pass (composer test)
 New functionality includes tests
 Documentation is updated (README.md, inline comments)
 Commits are meaningful and squashed if needed
 Feature branch created (not from main)
 Related issues are linked with "Fixes #issue-number"
 Backward compatibility is maintained
 CloudinaryEngine::PACKAGE_VERSION updated if needed

What Happens After Submission:

A maintainer will review your PR within a reasonable timeframe
You may receive feedback or change requests
Address feedback and push updates to your branch
Once approved and tests pass, your PR will be merged
Your contribution will be part of the next release

Important Notes:
This is a Cloudinary Labs project, which means:

It's an unsupported pre-production prototype
Code is provided "AS IS" without warranty
Avoid uploading confidential or personally identifiable information
All rights remain with Cloudinary

Despite this status, we maintain high quality standards and welcome contributions that improve the package.


Thank you for contributing to the Cloudinary Laravel SDK! Your efforts help make media management in Laravel applications better for everyone. We appreciate your time, expertise, and dedication to open source.
Remember: The best contributions come from understanding Laravel conventions, following coding standards, writing thorough tests, and documenting your work clearly.
Happy coding, and welcome to our community! 🚀

Made with ❤️ by the Cloudinary Labs community
Licensed under the MIT License.