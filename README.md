# YouTimeManager

**YouTimeManager** is an application that helps users calculate the total time of a YouTube playlist and estimate how many videos they can watch per day based on their daily viewing time limit. It also provides the expected end date for completing the playlist.

## Features

- **Total Playlist Duration**: Calculates the total duration of all videos in a given YouTube playlist.
- **Daily Watch Limit**: Allows users to input their daily maximum viewing time and estimates how many videos they can watch each day.
- **Completion Date Estimation**: Determines the date by which the user can finish watching the playlist, based on their daily limit.

## How It Works

1. The user enters the YouTube playlist URL.
2. The app fetches the playlist details and calculates the total time of all videos in the playlist.
3. The user enters the maximum number of hours they can spend watching YouTube daily.
4. The app calculates how many videos the user can watch per day and estimates the completion date based on the total playlist duration and the user's daily limit.

## Usage

1. Clone the repository:
    ```bash
    git clone https://github.com/ahmedibra3/YouTimeManager.git
    ```

2. Navigate to the project directory:
    ```bash
    cd YouTimeManager
    ```

3. Install dependencies:
    ```bash
    npm install
    ```

4. Run the application:
    ```bash
    npm start
    ```

5. Enter the playlist URL and your daily viewing time limit when prompted.

## Example

1. User provides the URL of a playlist containing 10 videos with a total duration of 5 hours and 30 minutes.
2. The user sets a daily limit of 1 hour.
3. The app calculates that the user can finish the playlist in approximately 6 days.

## Requirements

- PHP
- YouTube Data API (for fetching playlist details)
- A valid YouTube API key

## API Setup

1. Go to the [Google Developers Console](https://console.developers.google.com/).
2. Create a project and enable the YouTube Data API.
3. Generate an API key and add it to the environment variables as `YOUTUBE_API_KEY`.

## Contributing

Feel free to open issues or submit pull requests if you have suggestions or improvements.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
