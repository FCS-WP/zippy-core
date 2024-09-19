export const DateHelper = {
  convertDateToRange(dateString) {
    const date = new Date(dateString);

    // Set date_start to the beginning of the day
    const date_start = new Date(date);
    date_start.setHours(0, 0, 0, 0);

    // Set date_end to the end of the day
    const date_end = new Date(date);
    date_end.setHours(23, 59, 59, 999);

    return {
      date_start: date_start.toISOString(),
      date_end: date_end.toISOString(),
    };
  },
  weekToDate(year, week) {
    // Create a date object for the first day of the year
    const date = new Date(year, 0, 1);

    // Calculate the first day of the specified week (1 = Monday)
    const daysOffset = (week - 1) * 7 - date.getDay() + 1;
    date.setDate(date.getDate() + daysOffset);

    // Format the date to DD-MM-YYYY
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are 0-indexed
    const formattedDate = `${day}-${month}-${year}`;

    return formattedDate;
  },
};
