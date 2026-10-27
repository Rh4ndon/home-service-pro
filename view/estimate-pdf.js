/**
 * HomeService Pro - estimate / invoice PDF generator.
 *
 * Requires pdfMake (and its Roboto vfs fonts) to be loaded on the page:
 *   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
 *   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
 *
 * Exposes:
 *   buildEstimatePDF(estimate)     -> pdfMake document
 *   generateEstimatePDF(estimate)  -> downloads the PDF
 *   downloadEstimatePDF(ticketId)  -> fetches the estimate then downloads it
 */
(function () {
  function money(value) {
    var n = parseFloat(value);
    if (isNaN(n)) n = 0;
    return '\u20B1' + n.toLocaleString('en-PH', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function formatDate(value) {
    if (!value) return '—';
    var d = new Date(String(value).replace(' ', 'T'));
    if (isNaN(d.getTime())) return String(value);
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
  }

  function normalize(est) {
    est = est || {};

    var items = est.Items || est.items || [];
    if (!Array.isArray(items)) items = [];

    // Backwards-compatible single-row estimate shape.
    if (!items.length && (est.Materials || est.materials || est.Service || est.service)) {
      items = [{
        Materials: est.Materials || est.materials,
        Service: est.Service || est.service,
        Price: est.Price !== undefined ? est.Price : est.price
      }];
    }

    items = items.map(function (item) {
      return {
        materials: item.Materials || item.materials || '—',
        service: item.Service || item.service || '—',
        price: item.Price !== undefined ? item.Price : (item.price !== undefined ? item.price : 0)
      };
    });

    var total = est.Total !== undefined ? est.Total : (est.total !== undefined ? est.total : null);
    if (total === null) {
      total = items.reduce(function (sum, item) {
        var n = parseFloat(item.price);
        return sum + (isNaN(n) ? 0 : n);
      }, 0);
    }

    return {
      ticketId: est.Ticket_ID || est.ticket_id || est.ticketId || '',
      appliance: est.Appliance_Name || est.appliance_name || est.appliance || 'Appliance',
      items: items,
      total: total,
      createdAt: est.Created_At || est.created_at || ''
    };
  }

  function buildDocDefinition(estimate) {
    var est = normalize(estimate);
    var ticketLabel = est.ticketId ? 'Ticket #' + est.ticketId : 'Repair Service';

    var body = [[
      { text: 'NAME OF APPLIANCE', style: 'tableHeader' },
      { text: 'MATERIALS', style: 'tableHeader' },
      { text: 'SERVICE', style: 'tableHeader' },
      { text: 'PRICE', style: 'tableHeader', alignment: 'right' }
    ]];

    if (est.items.length) {
      est.items.forEach(function (item) {
        body.push([
          { text: est.appliance, style: 'tableCell' },
          { text: item.materials, style: 'tableCell' },
          { text: item.service, style: 'tableCell' },
          { text: money(item.price), style: 'tableCell', alignment: 'right' }
        ]);
      });
    } else {
      body.push([
        { text: est.appliance, style: 'tableCell' },
        { text: '—', style: 'tableCell' },
        { text: '—', style: 'tableCell' },
        { text: money(0), style: 'tableCell', alignment: 'right' }
      ]);
    }

    // Total row
    body.push([
      { text: 'TOTAL', colSpan: 3, alignment: 'right', bold: true, fontSize: 11, margin: [0, 8, 0, 8] },
      {},
      {},
      { text: money(est.total), bold: true, fontSize: 11, alignment: 'right', margin: [0, 8, 0, 8] }
    ]);

    return {
      pageSize: 'A4',
      pageMargins: [40, 40, 40, 40],
      content: [
        {
          text: 'HomeService Pro: Repair Service Estimate Cost',
          style: 'header',
          alignment: 'center',
          margin: [0, 0, 0, 6]
        },
        {
          text: ticketLabel + (est.createdAt ? '  •  ' + formatDate(est.createdAt) : ''),
          alignment: 'center',
          color: '#64748b',
          fontSize: 10,
          margin: [0, 0, 0, 24]
        },
        {
          table: {
            widths: ['34%', '22%', '28%', '16%'],
            body: body
          },
          layout: 'lightHorizontalLines'
        },
        {
          text: 'This document is an estimate prepared by the assigned repairman. Final charges may vary depending on the actual work and materials required.',
          fontSize: 9,
          color: '#64748b',
          margin: [0, 28, 0, 0]
        }
      ],
      styles: {
        header: {
          fontSize: 16,
          bold: true,
          color: '#0b1a26'
        },
        tableHeader: {
          bold: true,
          fontSize: 10,
          color: 'white',
          fillColor: '#1b8de8',
          margin: [0, 7, 0, 7]
        },
        tableCell: {
          fontSize: 10,
          margin: [0, 7, 0, 7]
        }
      },
      defaultStyle: {
        font: 'Roboto'
      }
    };
  }

  function download(estimate) {
    if (typeof pdfMake === 'undefined') {
      if (typeof showAlert === 'function') {
        showAlert('PDF generator is not available. Please check your internet connection and try again.', 'error');
      } else {
        alert('PDF generator is not available. Please check your internet connection and try again.');
      }
      return;
    }
    var est = normalize(estimate);
    var name = 'HomeService_Estimate' + (est.ticketId ? '_Ticket' + est.ticketId : '') + '.pdf';
    pdfMake.createPdf(buildDocDefinition(est)).download(name);
  }

  window.buildEstimatePDF = function (estimate) {
    return buildDocDefinition(estimate);
  };

  window.generateEstimatePDF = function (estimate) {
    download(estimate);
  };

  window.downloadEstimatePDF = function (source) {
    if (source && typeof source === 'object') {
      download(source);
      return;
    }

    var ticketId = source;
    if (!ticketId) return;

    var userId = '';
    try { userId = localStorage.getItem('id') || ''; } catch (e) {}

    fetch('../../controllers/customer/estimate.php?ticket_id=' + encodeURIComponent(ticketId) +
          '&user_id=' + encodeURIComponent(userId))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && data.status === 'success' && data.estimate) {
          download(data.estimate);
        } else {
          var msg = (data && data.message) || 'Unable to load the estimate.';
          if (typeof showAlert === 'function') showAlert(msg, 'error');
          else alert(msg);
        }
      })
      .catch(function () {
        if (typeof showAlert === 'function') showAlert('Network error while loading the estimate.', 'error');
        else alert('Network error while loading the estimate.');
      });
  };
})();
