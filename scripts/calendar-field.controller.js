(function() {
  
  function CalendarFieldController(inputEl)
  {
    this.inputEl = inputEl
    this.calendarEl = null
    this.monthSelectEl = null
    this.rowEls = []
    this.cellEls = []
    this.monthNames = ['January', 'February', 'March', 'April', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    this.monthNamesAbbreviated = ['Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    this.weekdayChars = ['M', 'T', 'W', 'T', 'F', 'S', 'S']
    this.calendarVisible = false
    
    this.inputEl.addEventListener('focus', this.inputFocus.bind(this))
    this.inputEl.addEventListener('click', this.inputClick.bind(this))
    document.body.addEventListener('click', this.bodyClicked.bind(this))
    document.body.addEventListener('keydown', this.bodyKeyDown.bind(this))
  }
  
  CalendarFieldController.prototype.inputFocus = function(ev)
  {
    this.showCalendar()
  }
  
  CalendarFieldController.prototype.inputClick = function(ev)
  {
    this.showCalendar()
  }
  
  CalendarFieldController.prototype.showCalendar = function()
  {
    if (!this.calendarEl) {
      this.createCalendarEl()
    }
    
    this.calendarEl.setAttribute('style', '')
    this.calendarVisible = true
  }
  
  CalendarFieldController.prototype.hideCalendar = function()
  {
    if (!this.calendarVisible) {
      return
    }
    
    this.calendarEl.setAttribute('style', 'display: none')
    this.calendarVisible = false
  }
  
  CalendarFieldController.prototype.bodyClicked = function(ev)
  {
    if (!this.calendarVisible) {
      return
    }
    
    if (ev.target == this.inputEl) {
      return
    }	
    
    var node = ev.target
    while (node != document.body) {
      if (node == this.calendarEl) {
        return
      }
      
      node = node.parentNode
    }
    
    this.hideCalendar()
  }
  
  CalendarFieldController.prototype.bodyKeyDown = function(ev)
  {
    if (!this.calendarVisible) {
      return
    }	
    
    var node = ev.target
    while (node != document.body) {
      if (node == this.calendarEl) {
        return
      }
      
      node = node.parentNode
    }
    
    this.hideCalendar()
  }
  
  CalendarFieldController.prototype.createCalendarEl = function()
  {
    this.calendarEl = document.createElement('div')
    this.calendarEl.setAttribute('class', 'date_field_calendar')
    this.calendarEl.setAttribute('style', 'display: none')
    
    var colCount = 7
    var rowCount = 6
    
    var today = new Date()
    
    // month/year select row
    var rowEl = document.createElement('div')
    rowEl.setAttribute('class', 'date_field_calendar_row date_field_calendar_month_year_row')
    
    this.monthSelectEl = document.createElement('select')
    this.monthSelectEl.setAttribute('tabindex', '-1')
    this.monthSelectEl.setAttribute('class', 'date_field_month_select')
    
    for (var monthIndex = 0; monthIndex < this.monthNames.length; monthIndex++) {
      var optionEl = document.createElement('option')
      optionEl.innerHTML = this.monthNames[monthIndex]
      
      if (today.getMonth() - 1 == monthIndex) {
        optionEl.setAttribute('selected', 'selected')
      }
      
      this.monthSelectEl.appendChild(optionEl)
    }
    rowEl.appendChild(this.monthSelectEl)
    
    this.yearSelectEl = document.createElement('select')
    this.yearSelectEl.setAttribute('tabindex', '-1')
    this.yearSelectEl.setAttribute('class', 'date_field_year_select')  
    var minYear = today.getFullYear()
    var maxYear = today.getFullYear() + 3
    for (var year = minYear; year <= maxYear; year++) {
      var optionEl = document.createElement('option')
      optionEl.innerHTML = year
      
      if (today.getFullYear() == year) {
        optionEl.setAttribute('selected', 'selected')
      }
      
      this.yearSelectEl.appendChild(optionEl)
    }
    rowEl.appendChild(this.yearSelectEl)
    
    this.calendarEl.appendChild(rowEl)
    
    // weekdays row
    var rowEl = document.createElement('div')
    rowEl.setAttribute('class', 'date_field_calendar_row date_field_calendar_weekdays_row')
    
    for (var colIndex = 0; colIndex < colCount; colIndex++) {
      var cellEl = document.createElement('span')
      cellEl.setAttribute('class', 'date_field_calendar_cell')
      cellEl.innerHTML = this.weekdayChars[colIndex]
      rowEl.appendChild(cellEl)
    }
    this.calendarEl.appendChild(rowEl)
    
    // calendar rows
    for (var rowIndex = 0; rowIndex < rowCount; rowIndex++) {
      var rowEl = document.createElement('div')
      rowEl.setAttribute('class', 'date_field_calendar_row')
      
      this.cellEls[rowIndex] = []
      for (var colIndex = 0; colIndex < colCount; colIndex++) {
        var cellEl = document.createElement('span')
        cellEl.setAttribute('class', 'date_field_calendar_cell')
        cellEl.innerHTML = '&nbsp;'
        rowEl.appendChild(cellEl)
        
        this.cellEls[rowIndex][colIndex] = cellEl
      }
      
      this.calendarEl.appendChild(rowEl)
      this.rowEls[rowIndex] = rowEl
    }
    
    this.loadMonth()
    
    this.inputEl.parentNode.appendChild(this.calendarEl)
  }
  
  CalendarFieldController.prototype.loadMonth = function()
  {
    var today = new Date()
    today.setHours(0)
    today.setMinutes(0)
    today.setSeconds(0)
    
    var firstOfMonth = new Date(today.getTime())
    firstOfMonth.setDate(1)
    
    var firstOfMonthColIndex = firstOfMonth.getDay() - 1
    if (firstOfMonthColIndex < 0) {
      firstOfMonthColIndex = 6
    }
    
    // the first date is probably in the previous month
    var date = new Date(firstOfMonth.getTime())
    date.setDate(firstOfMonth.getDate() - firstOfMonthColIndex)
    
    // fill cells
    var colCount = 7
    var rowCount = 6
    
    for (var rowIndex = 0; rowIndex < rowCount; rowIndex++) {
      for (var colIndex = 0; colIndex < colCount; colIndex++) {
        var cellEl = this.cellEls[rowIndex][colIndex]
        cellEl.innerHTML = date.getDate()
        
        var cellClass = 'date_field_calendar_cell'
        if (date.getMonth() != firstOfMonth.getMonth()) {
          cellClass += ' date_field_calendar_other_month_cell'
        }
        if (date.getTime() == today.getTime()) {
          cellClass += ' date_field_calendar_today_cell'
        }
        cellEl.setAttribute('class', cellClass)
        
        date.setDate(date.getDate() + 1)
      }
    }
    
    // fill out month cells
    
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    var nodes = document.getElementsByClassName('date_field')
    
    for (var nodeIndex = 0; nodeIndex < nodes.length; nodeIndex++) {
      var node = nodes[nodeIndex]
      if (node.tagName != 'INPUT') {
        continue
      }
      
      node.controller = new CalendarFieldController(node)
    }
  })
}())
