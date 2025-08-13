const createDigitalProductPanel = () => {
    Ext.define("App.model.DigitalProduct", {
        extend: "App.model.Product", // Hereda de Producto
        fields: [
            { name: "urlDescarga", type: "string" },
            { name: "licencia", type: "string" }
        ]
    });

    let digitalProductStore = Ext.create("Ext.data.Store", {
        storeId: "digitalProductStore",
        model: "App.model.DigitalProduct",
        proxy: {
            type: "rest",
            url: "/Api/productoDigital.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: 'json', rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Productos Digitales",
        store: digitalProductStore,
        itemId: "digitalProductPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 40, sortable: false, hideable: false, dataIndex: "id" },
            { text: "Nombre", flex: 1, sortable: false, hideable: false, dataIndex: "nombre" },
            { text: "Descripción", flex: 2, sortable: false, hideable: false, dataIndex: "descripcion" },
            { text: "Precio Unitario", flex: 1, sortable: false, hideable: false, dataIndex: "precioUnitario", renderer: Ext.util.Format.usMoney },
            { text: "Stock", flex: 1, sortable: false, hideable: false, dataIndex: "stock" },
            { text: "ID Categoría", flex: 1, sortable: false, hideable: false, dataIndex: "idCategoria" },
            { text: "URL Descarga", flex: 2, sortable: false, hideable: false, dataIndex: "urlDescarga" },
            { text: "Licencia", flex: 1, sortable: false, hideable: false, dataIndex: "licencia" }
        ],
    });

    return grid;
};
