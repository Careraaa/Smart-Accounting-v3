// Attendance table auto-refresh

function refreshAttendanceTable() {
    console.log(
        "Refreshing attendance table at",
        new Date().toLocaleTimeString(),
    );
    fetch(window.attendanceRowsUrl)
        .then((response) => response.json())
        .then((data) => {
            const tbody = document.getElementById("attendance-tbody");
            if (!tbody) return;

            // Define status styles once
            const statusStyles = {
                present: { bg: "#f0fdf4", color: "#16a34a", border: "#bbf7d0" },
                late: { bg: "#fffbeb", color: "#d97706", border: "#fde68a" },
                absent: { bg: "#fff1f2", color: "#e11d48", border: "#fcd0d0" },
            };

            let html = "";
            data.rows.forEach((att) => {
                const statusStyle = statusStyles[att.status] || {
                    bg: "#f4f5f7",
                    color: "#9898a8",
                    border: "#e8e8ef",
                };

                const entryType = att.is_manual
                    ? '<span class="emp-badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd;"><i class="feather-edit-2 me-1"></i>Manual</span>'
                    : '<span class="emp-badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;"><i class="feather-check-circle me-1"></i>QR Scanned</span>';

                html += `
                    <tr>
                        <td><strong>${att.employee}</strong></td>
                        <td><small class="text-muted">${att.date}</small></td>
                        <td>${att.time_in}</td>
                        <td>${att.time_out}</td>
                        <td class="text-center">
                            <span class="emp-badge" style="background:${statusStyle.bg}; color:${statusStyle.color}; border:1px solid ${statusStyle.border};">
                                ${att.status.replace(/_/g, " ").charAt(0).toUpperCase() + att.status.replace(/_/g, " ").slice(1)}
                            </span>
                        </td>
                        <td class="text-center">${entryType}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        })
        .catch((err) => {
            console.error("Failed to refresh attendance table", err);
        });
}

setInterval(refreshAttendanceTable, 30000);
refreshAttendanceTable();

console.log("Attendance auto-refresh initialized");
