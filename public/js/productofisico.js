const createPhysicalProductPanel = () => {
    Ext.define("App.model.PhysicalProduct", {
        extend: "App.model.Product", // Hereda de Producto
        fields: [
            { name: "peso", type: "float" },
            { name: "alto", type: "float" },
            { name: "ancho", type: "float" },
            { name: "profundidad", type: "float" }
        ]
    });

    let physicalProductStore = Ext.create("Ext.data.Store", {
        storeId: "physicalProductStore",
        model: "App.model.PhysicalProduct",
        proxy: {
            type: "rest",
            url: "/api/productoFisico.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: 'json', rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Productos Físicos",
        store: physicalProductStore,
        itemId: "physicalProductPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 40, sortable: false, hideable: false, dataIndex: "id" },
            { text: "Nombre", flex: 1, sortable: false, hideable: false, dataIndex: "nombre" },
            { text: "Descripción", flex: 2, sortable: false, hideable: false, dataIndex: "descripcion" },
            { text: "Precio Unitario", flex: 1, sortable: false, hideable: false, dataIndex: "precioUnitario", renderer: Ext.util.Format.usMoney },
            { text: "Stock", flex: 1, sortable: false, hideable: false, dataIndex: "stock" },
            { text: "ID Categoría", flex: 1, sortable: false, hideable: false, dataIndex: "idCategoria" },
            { text: "Peso", flex: 1, sortable: false, hideable: false, dataIndex: "peso" },
            { text: "Alto", flex: 1, sortable: false, hideable: false, dataIndex: "alto" },
            { text: "Ancho", flex: 1, sortable: false, hideable: false, dataIndex: "ancho" },
            { text: "Profundidad", flex: 1, sortable: false, hideable: false, dataIndex: "profundidad" }
        ],
    });

    return grid;
};
