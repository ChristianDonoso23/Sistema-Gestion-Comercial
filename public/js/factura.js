const createBillPanel = () => {
    Ext.define("App.model.Factura", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "idVenta", type: "int" },
            { name: "numero", type: "string" },
            { name: "claveAcceso", type: "string" },
            { name: "fechaEmision", type: "date", dateFormat: "Y-m-d" },
            { name: "estado", type: "string" }
        ]
    });

    let facturaStore = Ext.create("Ext.data.Store", {
        storeId: "facturaStore",
        model: "App.model.Factura",
        proxy: {
            type: "rest",
            url: "/Api/factura.php",
            reader: { type: "json", rootProperty: "" },
            writer: { type: "json", rootProperty: "", writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Facturas",
        store: facturaStore,
        itemId: "facturaPanel",
        layout: "fit",
        columns: [
            {
                text: "ID",
                width: 50,
                sortable: false,
                hideable: false,
                dataIndex: "id"
            },
            {
                text: "ID Venta",
                width: 80,
                sortable: false,
                hideable: false,
                dataIndex: "idVenta"
            },
            {
                text: "Número",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "numero"
            },
            {
                text: "Clave de Acceso",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "claveAcceso"
            },
            {
                text: "Fecha Emisión",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "fechaEmision",
                xtype: "datecolumn",
                format: "Y-m-d"
            },
            {
                text: "Estado",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "estado"
            }
        ],
        tbar: [
            {
                text: 'Eliminar Factura',
                handler() {
                    const rec = this.up('grid').getSelection()[0];
                    if (!rec) return Ext.Msg.alert('Atención', 'Seleccione una Factura para eliminar.');

                    Ext.Msg.confirm('Confirmar', '¿Está seguro de que desea eliminar la siguiente Factura?', btn => {
                        if (btn === 'yes') {
                            facturaStore.remove(rec);
                            facturaStore.sync({
                                success: () => Ext.Msg.alert('Éxito', 'Factura eliminada correctamente.'),
                                failure: () => Ext.Msg.alert('Error', 'No se pudo eliminar la Factura.')
                            });
                        }
                    });
                }
            }
        ]
    });

    return grid;
};
