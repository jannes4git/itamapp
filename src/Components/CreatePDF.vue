<template>
  <div>
    <button @click="generatePDF()">PDF erstellen</button>
  </div>
</template>
  
  <script>
import jsPDF from "jspdf";
import "jspdf-autotable";
import QRCode from "qrcode";

export default {
  data() {
    return {
      qrCodes: [
        {
          id: 1,
          label: "QR-Code 1",
          data: "http://localhost/nextcloud/index.php/apps/itamapp/#/asset/1",
        },
        {
          id: 2,
          label: "QR-Code 2",
          data: "http://localhost/nextcloud/index.php/apps/itamapp/#/asset/2",
        },
        {
          id: 3,
          label: "QR-Code 3",
          data: "http://localhost/nextcloud/index.php/apps/itamapp/#/asset/3",
        },
        {
          id: 4,
          label: "QR-Code 4",
          data: "http://localhost/nextcloud/index.php/apps/itamapp/#/asset/1",
        },
        {
          id: 5,
          label: "QR-Code 5",
          data: "http://localhost/nextcloud/index.php/apps/itamapp/#/asset/2",
        },
      ],
      qrCodeSize: 20, // 5 cm in Pixeln (bei 72 dpi)
      qrCodeSpacing: 20, // 4 cm in Pixeln (bei 72 dpi)
    };
  },
  methods: {
    async generatePDF() {
      const doc = new jsPDF({
        unit: "mm",
        format: "a4",
      });

      // Define layout and positions of QR codes
      const qrCodePositions = [
        { x: 20, y: 20 },
        { x: 20 + this.qrCodeSize + this.qrCodeSpacing, y: 20 },
        { x: 20 + 2 * (this.qrCodeSize + this.qrCodeSpacing), y: 20 },
      ];

      // Generate QR codes and place them at the defined positions
      for (let i = 0; i < this.qrCodes.length; i++) {
        const qrCodeData = await QRCode.toDataURL(this.qrCodes[i].data);
        doc.addImage(
          qrCodeData,
          "JPEG",
          qrCodePositions[i].x,
          qrCodePositions[i].y,
          this.qrCodeSize,
          this.qrCodeSize
        );
      }

      doc.save("qr_codes.pdf");
    },
  },
};
</script>
  
