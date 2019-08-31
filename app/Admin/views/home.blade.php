<div id="mountNode" style="text-align: center;"></div>
<script>/*Fixing iframe window.innerHeight 0 issue in Safari*/document.body.clientHeight;</script>
<script src="https://gw.alipayobjects.com/os/antv/pkg/_antv.g2-3.5.1/dist/g2.min.js"></script>
<script src="https://gw.alipayobjects.com/os/antv/pkg/_antv.data-set-0.10.1/dist/data-set.min.js"></script>
<script>
  var data = {!! $data !!}

  var ds = new DataSet();
  var dv = ds.createView().source(data);
  dv.transform({
    type: 'fold',
    fields: ['普通用户', '年费用户', '终身用户'], // 展开字段集
    key: '用户类型', // key字段
    value: '数量', // value字段
    // retains: ['name'] // 保留字段集，默认为除fields以外的所有字段
  });
  // 数据被加工成 {State: 'WY', 年龄段: '小于5岁', 数量: 25635}
  var chart = new G2.Chart({
    container: 'mountNode',
    // forceFit: true,
    width : 800, // 指定图表宽度
    // height : 300, // 指定图表高度
    height: window.innerHeight
  });
  chart.source(dv);
  chart.coord().transpose();
  chart.axis('name', {
    label: {
      offset: 12
    }
  });
  chart.intervalStack().position('name*数量').color('用户类型');
  chart.render();
</script>
