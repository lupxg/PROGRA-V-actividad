## Library Integrated System (LIS)
This is small LIS shipped with Docker. This project consist of an API written in PHP and a frontend programmed in HTML and JS. It also contains a desktop view programmed in C# using Avalonia.

- Backend API and desktop UI author: [Guillermo Steven Chávez Cubias](https://github.com/Chasty23)
- Infrastructure and web UI author: [Gerson Steven Chachagua Molina](https://github.com/lupxg)

# Installation
1. Clone this repository.
2. Copy the `.env.example` file and rename it to `.env`.
3. Create two text files under the `db` folder. These files will contain the database passwords ─ one for root and non-root.
4. Run `docker compose up -d` from the root directory.

# Disclamer
This is a college project, therefore it should not be used in production.