namespace Tris
{
    partial class Form1
    {
        /// <summary>
        /// Variabile di progettazione necessaria.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Pulire le risorse in uso.
        /// </summary>
        /// <param name="disposing">ha valore true se le risorse gestite devono essere eliminate, false in caso contrario.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Codice generato da Progettazione Windows Form

        /// <summary>
        /// Metodo necessario per il supporto della finestra di progettazione. Non modificare
        /// il contenuto del metodo con l'editor di codice.
        /// </summary>
        private void InitializeComponent()
        {
            this.btnGioca = new System.Windows.Forms.Button();
            this.lblDimensioneTavola = new System.Windows.Forms.Label();
            this.nudDimensioneTavola = new System.Windows.Forms.NumericUpDown();
            this.pnlTavola = new System.Windows.Forms.Panel();
            this.lblVincitore = new System.Windows.Forms.Label();
            this.lblVincitoreRisultato = new System.Windows.Forms.Label();
            this.lblTurno = new System.Windows.Forms.Label();
            this.lblTurnoValore = new System.Windows.Forms.Label();
            ((System.ComponentModel.ISupportInitialize)(this.nudDimensioneTavola)).BeginInit();
            this.SuspendLayout();
            // 
            // btnGioca
            // 
            this.btnGioca.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.btnGioca.Location = new System.Drawing.Point(12, 396);
            this.btnGioca.Name = "btnGioca";
            this.btnGioca.Size = new System.Drawing.Size(325, 62);
            this.btnGioca.TabIndex = 0;
            this.btnGioca.Text = "Gioca";
            this.btnGioca.UseVisualStyleBackColor = true;
            this.btnGioca.Click += new System.EventHandler(this.btnGioca_Click);
            // 
            // lblDimensioneTavola
            // 
            this.lblDimensioneTavola.AutoSize = true;
            this.lblDimensioneTavola.Location = new System.Drawing.Point(9, 13);
            this.lblDimensioneTavola.Name = "lblDimensioneTavola";
            this.lblDimensioneTavola.Size = new System.Drawing.Size(94, 13);
            this.lblDimensioneTavola.TabIndex = 1;
            this.lblDimensioneTavola.Text = "Dimensione tavola";
            // 
            // nudDimensioneTavola
            // 
            this.nudDimensioneTavola.Location = new System.Drawing.Point(13, 29);
            this.nudDimensioneTavola.Maximum = new decimal(new int[] {
            50,
            0,
            0,
            0});
            this.nudDimensioneTavola.Minimum = new decimal(new int[] {
            1,
            0,
            0,
            0});
            this.nudDimensioneTavola.Name = "nudDimensioneTavola";
            this.nudDimensioneTavola.Size = new System.Drawing.Size(324, 20);
            this.nudDimensioneTavola.TabIndex = 2;
            this.nudDimensioneTavola.Value = new decimal(new int[] {
            1,
            0,
            0,
            0});
            // 
            // pnlTavola
            // 
            this.pnlTavola.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.pnlTavola.Location = new System.Drawing.Point(343, 13);
            this.pnlTavola.Name = "pnlTavola";
            this.pnlTavola.Size = new System.Drawing.Size(445, 445);
            this.pnlTavola.TabIndex = 3;
            // 
            // lblVincitore
            // 
            this.lblVincitore.AutoSize = true;
            this.lblVincitore.Location = new System.Drawing.Point(9, 79);
            this.lblVincitore.Name = "lblVincitore";
            this.lblVincitore.Size = new System.Drawing.Size(51, 13);
            this.lblVincitore.TabIndex = 4;
            this.lblVincitore.Text = "Vincitore:";
            // 
            // lblVincitoreRisultato
            // 
            this.lblVincitoreRisultato.AutoSize = true;
            this.lblVincitoreRisultato.Location = new System.Drawing.Point(67, 79);
            this.lblVincitoreRisultato.Name = "lblVincitoreRisultato";
            this.lblVincitoreRisultato.Size = new System.Drawing.Size(0, 13);
            this.lblVincitoreRisultato.TabIndex = 5;
            // 
            // lblTurno
            // 
            this.lblTurno.AutoSize = true;
            this.lblTurno.Location = new System.Drawing.Point(9, 66);
            this.lblTurno.Name = "lblTurno";
            this.lblTurno.Size = new System.Drawing.Size(38, 13);
            this.lblTurno.TabIndex = 6;
            this.lblTurno.Text = "Turno:";
            // 
            // lblTurnoValore
            // 
            this.lblTurnoValore.AutoSize = true;
            this.lblTurnoValore.Location = new System.Drawing.Point(53, 66);
            this.lblTurnoValore.Name = "lblTurnoValore";
            this.lblTurnoValore.Size = new System.Drawing.Size(0, 13);
            this.lblTurnoValore.TabIndex = 7;
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(800, 470);
            this.Controls.Add(this.lblTurnoValore);
            this.Controls.Add(this.lblTurno);
            this.Controls.Add(this.lblVincitoreRisultato);
            this.Controls.Add(this.lblVincitore);
            this.Controls.Add(this.pnlTavola);
            this.Controls.Add(this.nudDimensioneTavola);
            this.Controls.Add(this.lblDimensioneTavola);
            this.Controls.Add(this.btnGioca);
            this.Name = "Form1";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Tris";
            ((System.ComponentModel.ISupportInitialize)(this.nudDimensioneTavola)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Button btnGioca;
        private System.Windows.Forms.Label lblDimensioneTavola;
        private System.Windows.Forms.NumericUpDown nudDimensioneTavola;
        private System.Windows.Forms.Panel pnlTavola;
        private System.Windows.Forms.Label lblVincitore;
        private System.Windows.Forms.Label lblVincitoreRisultato;
        private System.Windows.Forms.Label lblTurno;
        private System.Windows.Forms.Label lblTurnoValore;
    }
}

