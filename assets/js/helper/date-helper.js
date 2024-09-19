export const DateHelper = {
  convertDateToRange(dateString) {
    const date = new Date(dateString);

    const date_start = new Date(date);
    date_start.setHours(0, 0, 0, 0);

    const date_end = new Date(date);
    date_end.setHours(23, 59, 59, 999);

    return {
      date_start: date_start.toISOString(),
      date_end: date_end.toISOString(),
    };
  },
  getDateOutputSelect(dateData, type) {
    const { date_start, date_end } = dateData;

    const removeTimeFromDate = (dateString) => {
      return dateString.split(" ")[0]; // Split by space and return the date part
    };

    switch (type) {
      case "week":
        return `${removeTimeFromDate(date_start)} - ${removeTimeFromDate(
          date_end
        )}`;
      case "month":
        return `${removeTimeFromDate(date_start)} - ${removeTimeFromDate(
          date_end
        )}`;
      default:
        return removeTimeFromDate(date_start);
    }
  },
  convertDateOutputChart(dateData, type) {
    switch (type) {
      case "week":
        const [yearDataWeek, weekDataWeek] = dateData.split("-");

        // Create a date object for the first day of the year
        const date = new Date(yearDataWeek, 0, 1);

        // Calculate the first day of the specified week (1 = Monday)
        const daysOffset = (weekDataWeek - 1) * 7 - date.getDay() + 1;
        date.setDate(date.getDate() + daysOffset);

        // Format the date to DD-MM-YYYY
        const day = String(date.getDate()).padStart(2, "0");
        const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are 0-indexed
        const formattedDate = `${yearDataWeek}-${month}-${day}`;

        return formattedDate;
      case "month":
        const [yearDataMonth, monthDataMonth] = dateData.split("-");

        const monthDate = `${yearDataMonth}-${monthDataMonth}`;

        return monthDate;
      default:
        return dateData;
    }
  },
  getWeekStartAndEnd(dateData) {
    const date = new Date(dateData);
    const dayOfWeek = date.getDay(); // 0 (Sunday) to 6 (Saturday)

    const startOfWeek = new Date(date);
    startOfWeek.setDate(date.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1));

    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);

    const formatStartDate = (d) =>
      `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(
        d.getDate()
      ).padStart(2, "0")} 00:00:00`;
    const formatEndDate = (d) =>
      `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(
        d.getDate()
      ).padStart(2, "0")} 23:59:59`;
    return {
      date_start: formatStartDate(startOfWeek),
      date_end: formatEndDate(endOfWeek),
    };
  },
  getMonthStartEnd(monthYear) {
    const [year, month] = monthYear.split("-").map(Number);

    // Start date is the first day of the month at 00:00:00
    const startDate = new Date(Date.UTC(year, month - 1, 1, 0, 0, 0));

    // End date is the last day of the month at 23:59:59
    const endDate = new Date(Date.UTC(year, month, 0, 23, 59, 59));

    // Format the dates
    const formatDate = (date) => {
      const yyyy = date.getUTCFullYear();
      const mm = String(date.getUTCMonth() + 1).padStart(2, "0");
      const dd = String(date.getUTCDate()).padStart(2, "0");
      const hh = String(date.getUTCHours()).padStart(2, "0");
      const min = String(date.getUTCMinutes()).padStart(2, "0");
      const sec = String(date.getUTCSeconds()).padStart(2, "0");
      return `${yyyy}-${mm}-${dd} ${hh}:${min}:${sec}`;
    };

    return {
      start: formatDate(startDate),
      end: formatDate(endDate),
    };
  },
  getDatesOfCurrentYear() {
    const currentYear = new Date().getFullYear();
    const dates = [];

    for (let month = 0; month < 12; month++) {
      for (let day = 1; day <= 31; day++) {
        const date = new Date(currentYear, month, day);

        // Check if the date is valid
        if (date.getFullYear() === currentYear && date.getMonth() === month) {
          // Format the date to DD-MM-YYYY
          const formattedDate = `${String(date.getDate()).padStart(
            2,
            "0"
          )}-${String(date.getMonth() + 1).padStart(2, "0")}-${currentYear}`;
          dates.push(formattedDate);
        }
      }
    }

    return dates;
  },
  getDate(type) {
    switch (type) {
      case "custom":
        const startOfMonth = `${this.getDayStartMonth()} 00:00:00`;
        const endOfMonth = `${this.getDayEndMonth()} 23:59:00`;
        return { date_end: endOfMonth, date_start: startOfMonth };

      default:
        const today = new Date();

        const startDate = new Date(today);
        startDate.setDate(today.getDate() - 6); // 6 days before today

        const endDate = today;

        const formatDate = (date) => date.toISOString().split("T")[0];

        return {
          date_start: `${formatDate(startDate)} 00:00:00`,
          date_end: `${formatDate(endDate)} 23:59:00`,
        };
    }
  },
  getDayStartMonth() {
    const now = new Date();

    // Create a new Date object for the start of the current month
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

    // Format the date as DD/MM/YYYY
    const day = String(startOfMonth.getDate()).padStart(2, "0");
    const month = String(startOfMonth.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
    const year = startOfMonth.getFullYear();

    const formattedDate = `${year}-${month}-${day}`;
    return formattedDate;
  },

  getDayEndMonth() {
    const now = new Date();

    // Create a new Date object for the start of the current month
    // Format the date as DD/MM/YYYY
    const day = String(now.getDate()).padStart(2, "0");
    const month = String(now.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
    const year = now.getFullYear();

    const formattedDate = `${year}-${month}-${day}`;
    return formattedDate;
  },

  getDateToString(date, time) {
    const DayObject = new Date(date);
    const day = String(DayObject.getDate()).padStart(2, "0");
    const month = String(DayObject.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
    const year = DayObject.getFullYear();

    return `${year}-${month}-${day} ${time}`;
  },
};
