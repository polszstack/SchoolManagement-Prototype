
<style>
* { box-sizing: border-box; margin: 0; }
body {
  font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
  background: #f0f4f8;
  margin: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem 1.5rem;
}
h2 {
  font-weight: 600;
  font-size: 2rem;
  letter-spacing: -0.01em;
  color: #0a2540;
  margin-bottom: 1.8rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  max-width: 1400px;
}
h2::before { content: ""; font-size: 2rem; }
.container {
  display: flex;
  gap: 1.8rem;
  flex-wrap: wrap;
  max-width: 1400px;
  width: 100%;
}
.box {
  background: #ffffff;
  padding: 1.8rem 1.8rem 2rem;
  border-radius: 28px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.05), 0 4px 10px rgba(0,20,30,0.02);
}
.form {
  width: 250px;
  display: flex;
  flex-direction: column;
}
.table-box {
  flex: 1;
  min-width: 750px;
  overflow-x: auto;
  padding-right: 0.5rem;
}
h3 {
  font-size: 1.6rem;
  font-weight: 500;
  margin: 0 0 1.2rem 0;
  color: #1e293b;
  border-left: 6px solid #3498db;
  padding-left: 1rem;
}
input, select, button {
  width: 100%;
  padding: 0.85rem 1rem;
  margin: 0.4rem 0 0.8rem 0;
  border-radius: 18px;
  border: 1.5px solid #e2e8f0;
  font-size: 0.95rem;
  background: #fff;
  transition: 0.15s;
  outline: none;
  font-family: inherit;
}
input:focus, select:focus {
  border-color: #3498db;
  box-shadow: 0 0 0 4px rgba(52,152,219,0.15);
}
button {
  background: #3498db;
  color: white;
  font-weight: 600;
  border: none;
  margin-top: 0.8rem;
  cursor: pointer;
  border-radius: 40px;
  font-size: 1rem;
  letter-spacing: 0.3px;
  transition: background 0.2s, transform 0.1s;
}
button:hover {
  background: #217dbb;
  box-shadow: 0 8px 16px -6px #3498db80;
}
button:active { transform: scale(0.98); }
.edit, .delete {
  width: auto;
  padding: 0.4rem 1rem;
  margin: 0 0.2rem;
  border-radius: 40px;
  font-size: 0.85rem;
  font-weight: 500;
  display: inline-block;
  border: none;
}
.edit {
  background: #f39c12;
  color: #1e1e2f;
}
.edit:hover { background: #e08e0b; }
.delete {
  background: #e74c3c;
}
.delete:hover { background: #c0392b; }
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}
th {
  background: #1e293b;
  color: #f8fafc;
  font-weight: 500;
  padding: 1rem 0.6rem;
  white-space: nowrap;
}
th:first-child { border-radius: 18px 0 0 18px; }
th:last-child { border-radius: 0 18px 18px 0; }
td {
  padding: 0.9rem 0.5rem;
  border-bottom: 1px solid #e9edf2;
  text-align: center;
  vertical-align: middle;
}
/* status badge styles */
.status-badge {
  display: inline-block;
  padding: 0.25rem 0.9rem;
  border-radius: 40px;
  font-weight: 600;
  font-size: 0.8rem;
  letter-spacing: 0.3px;
  text-transform: uppercase;
  background: #e2e8f0;
  color: #1e293b;
}
.status-out { background: #ef4444; color: white; }
.status-low { background: #f59e0b; color: white; }
.status-ok { background: #10b981; color: white; }
#cancelBtn {
  background: #475569;
  margin-top: 0.2rem;
  display: none;
}
#cancelBtn:hover { background: #334155; }
/* action cell */
td:last-child {
  display: flex;
  gap: 0.3rem;
  justify-content: center;
  border-bottom: 1px solid #e9edf2;
  padding: 0.7rem 0.2rem;
}
td:last-child button {
  margin: 0;
  width: auto;
  flex: 0 1 auto;
}
</style>
<div class="module-header">
  <h2>Medication & Inventory</h2>
</div>
<div class="container">

  <!-- left form card (ARRIVED DATE added) -->
  <div class="box form">
    <h3 id="formTitle"> Add medication</h3>
    <input type="hidden" id="editIndex" value="-1">

    <label style="font-weight:500;">Medicine name</label>
    <input type="text" id="name" placeholder="e.g. Amoxicillin" autocomplete="off">

    <label style="font-weight:500;">Quantity</label>
    <input type="number" id="qty" placeholder="0" min="0" step="1">

    <label style="font-weight:500;">Expiry date</label>
    <input type="date" id="expiry">

    <!-- NEW ARRIVED DATE FIELD -->
    <label style="font-weight:500;">Arrived date</label>
    <input type="date" id="arrived">

    <label style="font-weight:500;">Type</label>
    <select id="type">
      <option value="" disabled selected>— select type —</option>
      <option>Tablet</option>
      <option>Capsule</option>
      <option>Syrup</option>
      <option>Injection</option>
    </select>

    <button onclick="saveMed()" id="mainSaveBtn"> Save medication</button>
    <button onclick="cancelEdit()" id="cancelBtn"> Cancel editing</button>
  </div>

  <!-- right table card (with two new columns) -->
  <div class="box table-box">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Qty</th>
          <th>Expiry</th>
          <th>Arrived</th>          <!-- NEW -->
          <th>Type</th>
          <th>Status</th>            <!-- NEW (Low stock / Out of stock / Available) -->
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="table"></tbody>
    </table>
    <div style="margin-top: 1.2rem; font-size:0.9rem; color:#4b5563; display: flex; gap: 1.5rem; align-items:center; flex-wrap:wrap;">
      <span><span style="background:#ef4444; color:white; padding:0.2rem 1rem; border-radius:40px;">Out</span> = 0 qty</span>
      <span><span style="background:#f59e0b; color:white; padding:0.2rem 1rem; border-radius:40px;">Low</span> ≤ 5</span>
      <span><span style="background:#10b981; color:white; padding:0.2rem 1rem; border-radius:40px;">In stock</span> ≥ 6</span>
      <span style="margin-left:auto;"> </span>
    </div>
  </div>
</div>

<script>
(function() {
  // ---------- initial demo data (includes arrived date) ----------
  let meds = [
    { name: "Amoxicillin", qty: 12, expiry: "2025-08-20", arrived: "2024-05-10", type: "Capsule" },
    { name: "Ibuprofen", qty: 5, expiry: "2024-12-01", arrived: "2024-02-15", type: "Tablet" },
    { name: "Cough syrup", qty: 2, expiry: "2025-03-10", arrived: "2024-01-20", type: "Syrup" },
    { name: "Insulin", qty: 8, expiry: "2024-06-15", arrived: "2024-04-01", type: "Injection" },
    { name: "Loratadine", qty: 0, expiry: "2025-11-05", arrived: "2023-12-11", type: "Tablet" }  // out of stock example
  ];

  // state
  let editIndex = -1;  // -1 = add mode

  // DOM elements
  const nameInp = document.getElementById('name');
  const qtyInp = document.getElementById('qty');
  const expiryInp = document.getElementById('expiry');
  const arrivedInp = document.getElementById('arrived');   // new
  const typeSelect = document.getElementById('type');
  const formTitle = document.getElementById('formTitle');
  const cancelBtn = document.getElementById('cancelBtn');
  const editIndexHidden = document.getElementById('editIndex');

  // reset form to add mode (clear all fields)
  function resetForm() {
    nameInp.value = '';
    qtyInp.value = '';
    expiryInp.value = '';
    arrivedInp.value = '';   // clear arrived
    typeSelect.value = '';
    editIndex = -1;
    editIndexHidden.value = -1;
    formTitle.innerText = ' Add medication';
    cancelBtn.style.display = 'none';
  }

  // helper: get status text & class based on qty
  function getStatusInfo(qty) {
    if (qty <= 0) return { text: 'OUT', class: 'status-out' };
    if (qty <= 5) return { text: 'LOW', class: 'status-low' };
    return { text: 'IN STOCK', class: 'status-ok' };
  }

  // render table
  function render() {
    const tbody = document.getElementById('table');
    if (!tbody) return;
    let html = '';
    meds.forEach((m, i) => {
      // safety
      const name = m.name || '—';
      const qty = m.qty ?? 0;
      const expiry = m.expiry || '—';
      const arrived = m.arrived || '—';     // new column
      const type = m.type || '—';

      const status = getStatusInfo(qty);
      // qty cell can also show low class if needed (keep original low style optional, but we also have badge)
      const lowClass = (qty <= 5 && qty > 0) ? 'low' : '';  // we keep low class for qty number style (optional)
      // we'll apply low class only to the Qty span (red background), but it's fine

      html += `<tr>
        <td>${escapeHtml(name)}</td>
        <td><span class="${qty <= 5 && qty > 0 ? 'low' : ''}">${escapeHtml(qty)}</span></td>
        <td>${escapeHtml(expiry)}</td>
        <td>${escapeHtml(arrived)}</td>            <!-- arrived column -->
        <td>${escapeHtml(type)}</td>
        <td><span class="status-badge ${status.class}">${status.text}</span></td>
        <td>
          <button class="edit" onclick="editMed(${i})"> Edit</button>
          <button class="delete" onclick="deleteMed(${i})"> Delete</button>
        </td>
      </tr>`;
    });
    tbody.innerHTML = html || `<tr><td colspan="7" style="text-align:center; padding:2.5rem; color:#64748b;">📭 No medications. Add one using the form.</td></tr>`;
  }

  function escapeHtml(unsafe) {
    if (unsafe === undefined || unsafe === null) return '';
    return String(unsafe).replace(/[&<>"]/g, function(m) {
      if(m === '&') return '&amp;'; if(m === '<') return '&lt;'; if(m === '>') return '&gt;'; if(m === '"') return '&quot;';
      return m;
    });
  }

  // Save (add or update)
  window.saveMed = function() {
    const name = nameInp.value.trim();
    const qty = qtyInp.value.trim();
    const expiry = expiryInp.value;
    const arrived = arrivedInp.value;   // new field
    const type = typeSelect.value;

    if (!name) { alert(' Please enter medicine name'); nameInp.focus(); return; }
    if (!qty) { alert(' Quantity is required'); qtyInp.focus(); return; }
    if (isNaN(qty) || Number(qty) < 0) { alert(' Quantity must be 0 or more'); qtyInp.focus(); return; }
    if (!expiry) { alert(' Select an expiry date'); expiryInp.focus(); return; }
    // arrived date is optional? We'll make it recommended but not mandatory. If empty, set to empty string.
    // But we can require it for consistency – uncomment next line to make arrived mandatory.
    // if (!arrived) { alert(' Select arrived date'); arrivedInp.focus(); return; }

    const qtyNum = Number(qty);

    if (editIndex === -1) {
      // add new
      meds.push({ name, qty: qtyNum, expiry, arrived: arrived || '', type });
    } else {
      // update existing
      if (editIndex >= 0 && editIndex < meds.length) {
        meds[editIndex] = { name, qty: qtyNum, expiry, arrived: arrived || '', type };
      } else {
        alert('Editing error — resetting.');
        editIndex = -1;
      }
    }

    resetForm();
    render();
  };

  window.editMed = function(index) {
    if (index < 0 || index >= meds.length) return;
    const m = meds[index];
    nameInp.value = m.name || '';
    qtyInp.value = m.qty;
    expiryInp.value = m.expiry || '';
    arrivedInp.value = m.arrived || '';   // populate arrived
    typeSelect.value = m.type || '';

    editIndex = index;
    editIndexHidden.value = index;
    formTitle.innerText = ' Edit medication';
    cancelBtn.style.display = 'block';
    document.querySelector('.form').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  };

  window.deleteMed = function(index) {
    if (index < 0 || index >= meds.length) return;
    if (confirm(' Delete this medication?')) {
      meds.splice(index, 1);
      if (editIndex === index) {
        resetForm();
      } else if (editIndex > index) {
        editIndex -= 1;
        editIndexHidden.value = editIndex;
      }
      render();
    }
  };

  window.cancelEdit = function() {
    resetForm();
    render();
  };

  // initial render
  window.onload = function() {
    render();
    resetForm();
  };

  // expose for console
  window.meds = meds;
})();
</script>

</body>
</html>