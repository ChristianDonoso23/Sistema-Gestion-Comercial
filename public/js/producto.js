const createProductsPanel = () => {
    Ext.define("App.model.Producto", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "tipo", type: "string" },
            { name: "nombre", type: "string" },
            { name: "descripcion", type: "string" },
            { name: "precioUnitario", type: "float" },
            { name: "stock", type: "int" },
            { name: "idCategoria", type: "int" },
            { name: "peso", type: "float", defaultValue: 0 },
            { name: "alto", type: "float", defaultValue: 0 },
            { name: "ancho", type: "float", defaultValue: 0 },
            { name: "profundidad", type: "float", defaultValue: 0 },
            { name: "urlDescarga", type: "string", defaultValue: "" },
            { name: "licencia", type: "string", defaultValue: "" },
            {
                name: "dimensiones",
                convert: (v, rec) => {
                    if (rec.get("tipo") === "ProductoFisico") {
                        return `${rec.get("peso")}kg / ${rec.get("alto")}x${rec.get("ancho")}x${rec.get("profundidad")} cm`;
                    }
                    return "";
                }
            }
        ]
    });

    const productoStore = Ext.create("Ext.data.Store", {
        storeId: "productoStore",
        model: "App.model.Producto",
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "Api/producto.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Listado de Productos",
        store: productoStore,
        itemId: "productPanel",
        layout: "fit",
        tbar: [
            {
                text: "General",
                handler: () => productoStore.clearFilter()
            },
            {
                text: "Productos Físicos",
                handler: () => {
                    productoStore.clearFilter();
                    productoStore.filterBy(rec => rec.get("tipo") === "ProductoFisico");
                }
            },
            {
                text: "Productos Digitales",
                handler: () => {
                    productoStore.clearFilter();
                    productoStore.filterBy(rec => rec.get("tipo") === "ProductoDigital");
                }
            }
        ],
        columns: [
            { 
                text: "ID", 
                width: 40, 
                sortable: false, 
                hideable: false, 
                dataIndex: "id" 
            },
            { 
                text: "Tipo", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "tipo" 
            },
            { 
                text: "Nombre", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "nombre" 
            },
            { 
                text: "Descripción", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "descripcion" 
            },
            { 
                text: "Precio Unitario", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "precioUnitario" 
            },
            { 
                text: "Stock", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "stock" 
            },
            { 
                text: "Categoría", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "idCategoria" 
            },
            { 
                text: "Dimensiones / Peso", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "dimensiones" 
            },
            { 
                text: "URL Descarga", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "urlDescarga" 
            },
            { 
                text: "Licencia", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "licencia" 
            }
        ]
    });

    return grid;
};

window.createProductsPanel = createProductsPanel;
